<?php

namespace Evelyn\App;

class PrivateShortcodes
{


    public function __construct()
    {
    }

    public function initShortcode()
    {

        add_shortcode('ev_chat_block', array($this, 'ev_chat_block'));
    }
    public function ev_chat_block()
    {
        // global $wpdb;
        // $all_conversation = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}yobro_conversation WHERE sender = %d OR reciever = %d ORDER BY created_at DESC;", array(2, 2)));
        // var_dump($all_conversation);

        // $last_message = $wpdb->get_row(
        //     $wpdb->prepare(
        //         "SELECT * FROM {$wpdb->prefix}yobro_messages WHERE conv_id = %d AND delete_status != 1 ORDER BY id DESC;",
        //         3
        //     ),
        //     ARRAY_A
        // );
        // var_dump($last_message);
        $users = get_all_users();
        $user_info = get_user_profiles_data(get_current_user_id());
        ev_localize_scripts();
        wp_localize_script('app-frontend-ev', 'USERS_EV', array('users' => $users));
        wp_localize_script('app-frontend-ev', 'USER', array('user' => $user_info));
        if (is_user_logged_in()) {
            return '<div id="ev_chatbox"></div>';
        } else {
            return '<a href="' . wp_login_url() . '">Please Log In</a>';
        }
    }
}
