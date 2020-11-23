<?php

function get_all_users()
{

    $users = get_users();

    $publish = [];

    foreach ($users as $user) {
        if (get_current_user_id() !== $user->ID) {
            $publish[] = array(
                'id' => $user->ID,
                'name' => get_users_name_by_id($user->ID),
                'pic' => get_avatar_url($user->ID),
                'username' => $user->user_login
            );
        }
    }

    return $publish;
}

function get_users_name_by_id($user_id)
{
    $user = get_user_by('id', $user_id);

    if (isset($user) && !empty($user)) {
        $fullName = "{$user->first_name}  {$user->last_name}";
        if (trim($fullName) == '') {
            return $user->user_login;
        }
        return $fullName;
    }
}

function ev_localize_scripts()
{

    $all_images = get_posts(array(
        'author' => get_current_user_id(),
        'post_status' => 'any',
        'post_type' => 'attachment',
        'posts_per_page' => -1
    ));
    wp_localize_script('app-frontend-ev', 'API_EV', array(
        'nonce' => wp_create_nonce('ev_nonce'),
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'images' => $all_images
    ));

    wp_localize_script('app-frontend-ev', 'INBOX_EV', array(
        'conversations' => get_user_conversations(get_current_user_id()),
        'unseen' => get_autopush_messages_private()

    ));
}
function get_user_profiles_data($user_id)
{
    $user_data = [];

    $current_user = wp_get_current_user();
    $user_data['id'] = $current_user->ID;
    $user_data['fname'] = $current_user->first_name;
    $user_data['lname'] = $current_user->last_name;
    $user_data['email'] = $current_user->user_email;

    $user_meta = get_user_meta($user_id);
    if (!is_wp_error($current_user)) {
        foreach ($user_meta as $meta => $value) {
            $user_data[$meta] = $value;
        }
    }
    return $user_data;
}

function get_user_conversations($user_id)
{
    global $wpdb;
    $all_conversation = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}yobro_conversation WHERE sender = %d OR reciever = %d ORDER BY created_at DESC;", array($user_id, $user_id)), ARRAY_A);
    if (!empty($all_conversation)) {

        foreach ($all_conversation as &$conversation) {
            $conversation['sender_name'] = get_users_name_by_id($conversation['sender']);
            $conversation['reciever_name'] = get_users_name_by_id($conversation['reciever']);
            $conversation['conv_id'] = $conversation['id'];
            if ($user_id == $conversation['sender']) {
                $conversation['name'] = $conversation['reciever_name'];

                if (get_avatar($conversation['reciever'])) {
                    $conversation['pic'] = get_avatar_url($conversation['reciever']);
                }
            } else {
                $conversation['name'] = $conversation['sender_name'];
                if (get_avatar($conversation['sender'])) {
                    $conversation['pic'] = get_avatar_url($conversation['sender']);
                }
            }

            $last_message = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM {$wpdb->prefix}yobro_messages WHERE conv_id = %d AND delete_status != 1 ORDER BY id DESC;",
                    $conversation['id']
                ),
                ARRAY_A
            );
            if (!empty($last_message)) {
                $conversation['message'] = sanitize_text_field(encrypts_decrypts($last_message['message'], $last_message['sender_id'], 'decrypt'));
                $conversation['message_id'] = $last_message['id'];
                $conversation['time'] = $last_message['created_at'];
                $conversation['last_sender'] = $last_message['sender_id'];
                $conversation['message_exists'] = 'true';

                if ($last_message['sender_id'] != get_current_user_id()) {
                    $conversation['seen'] = $last_message['seen'] != 1 ? false : true;
                } else {
                    $conversation['seen'] = true;
                }
            } else {
                $conversation['time'] = $conversation['created_at'];
                $conversation['message_exists'] = 'false';
                $conversation['message'] = '';
            }
        }

        $time = array_column($all_conversation, 'time');
        array_multisort($time, SORT_DESC, $all_conversation);
    } else {
        $all_conversation = array();
    }
    return $all_conversation;
}

function encrypts_decrypts($string, $user_id, $action = 'encrypt')
{
    // $user_id = get_current_user_id();
    $secret_key = $user_id;
    $secret_iv = $user_id;

    $output = false;
    $encrypt_method = "AES-256-CBC";
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    if ($action == 'encrypt') {
        $output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
    } else if ($action == 'decrypt') {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}

///////MESSAGES

function get_some_messages($conv_id)
{
    global $wpdb;
    $user_id = get_current_user_id();
    $messages = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}yobro_messages WHERE conv_id = %d AND delete_status != 1 AND ( sender_id = %d OR reciever_id = %d);",
            array($conv_id, $user_id, $user_id)
        ),
        ARRAY_A
    );
    if (!empty($messages) && !is_wp_error($messages)) {
        foreach ($messages as &$message) {
            $message['message'] = sanitize_text_field(encrypts_decrypts($message['message'], $message['sender_id'], 'decrypt'));
            if ($message['sender_id'] == get_current_user_id()) {
                $message['owner'] = 'true';
            } else {
                $message['owner'] = 'false';
            }
            $message['pic'] = get_avatar_url($message['sender_id'], array('size' => 30));
            $message['sender_name'] =  get_users_name_by_id($message['sender_id']);
            $message['reciever_name'] =  get_users_name_by_id($message['reciever_id']);
        }
    }
    return $messages;
}

function do_store_private_message(array $message)
{

    global $wpdb;
    $new_message = array(
        'conv_id' =>  intval($message['convid']),
        'sender_id' =>  intval($message['sender_id']),
        'reciever_id' =>  intval($message['reciever_id']),
        'message' => sanitize_text_field(encrypts_decrypts($message['message'], $message['sender_id'])),
        'created_at' => date("Y-m-d H:i:s")
    );
    $insert =  $wpdb->insert(
        "{$wpdb->prefix}yobro_messages",
        $new_message,
        array('%d', '%d', '%d', '%s', '%s')
    );
    if (!is_wp_error($insert)) {
        $new_message['message'] = sanitize_text_field($message['message']);
        if ($new_message['sender_id'] == get_current_user_id()) {
            $new_message['owner'] = 'true';
        } else {
            $new_message['owner'] = 'false';
        }
        $new_message['sender_name'] =  get_users_name_by_id($new_message['sender_id']);
        $new_message['reciever_name'] =  get_users_name_by_id($new_message['reciever_id']);
    }
    return $new_message;
}
function get_inbox_all_messages($data) {
    
}

function get_autopush_messages_private($conv_id, $last_message)
{

    global $wpdb;
    if ($last_message['sender_id'] = get_current_user_id()) {
        $reciever_id = $last_message['reciever_id'];
    } else {
        $reciever_id = $last_message['sender_id'];
    }

    $unseen_messages = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}yobro_messages WHERE conv_id = %d AND sender_id = %d AND delete_status != 1 AND seen = null;",
            array($conv_id, $reciever_id)
        ),
        ARRAY_A
    );

    return $unseen_messages;
}
