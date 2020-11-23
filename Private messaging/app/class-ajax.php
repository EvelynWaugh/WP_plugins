<?php

namespace Evelyn\App;

class PrivateAjax
{
    public function __construct()
    {

        $ajaxs = [
            'grab_conv_message' => true,
            'post_private_message' => true
        ];
        foreach ($ajaxs as $ajax => $value) {
            add_action('wp_ajax_' . $ajax, array($this, $ajax));
            add_action('wp_ajax_nopriv_' . $ajax, array($this, $ajax));
        }

        // add_action('wp_ajax_grab_conv_message', array($this, 'grab_conv_message'));
        // add_action('wp_ajax_nopriv_grab_conv_message', array($this, 'grab_conv_message'));
    }
    public function grab_conv_message()
    {

        if (isset($_POST['convId']) && !empty($_POST['convId'])) {
            $convId = $_POST['convId'];
            $messages = apply_filters('grab_messages_react', get_some_messages($convId));
            wp_send_json(array(
                'messages' => $messages
            ));
        }

        // echo 'ok';
        wp_die();
    }
    public function post_private_message()
    {


        if (isset($_POST['message']) && !empty(trim($_POST['message']))) {
            $message = apply_filters('push_new_message_todb', do_store_private_message($_POST));
            // $message = sanitize_text_field( $_POST['message'] );
            do_action('evelyn_after_private_message', $message);
            $message['pic'] = get_avatar_url($message['sender_id'], array('size' => 30));
            $message['time'] = $message['created_at'];
            wp_send_json(array(
                'pushed' => true,
                'message' => $message
            ));
        }
        wp_die();
    }
}
