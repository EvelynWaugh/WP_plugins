<?php

namespace Evelyn\Ext\Social_Login\Networks;

use Exception;

abstract class Network
{
    public function handle_request($request)
    {
        $this->request = $request;
        $process = !empty($request['process']) ? $request['process'] : "";
        $this->get_user_data();
        if (in_array($process, ['login', 'connect'])) {
            try {
                $this->{$process}();

                return wp_send_json(array(
                    'status' => 'success',
                    'name' => get_user_meta(get_current_user_id(), 'ev_facebook_account_name', true),
                    'picture' => $this->get_picture_url(get_current_user_id())

                ));
            } catch (\Exception $e) {
                return wp_send_json([
                    'status'  => 'error',
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return wp_send_json([
            'status'  => 'login_invalid',
            'message' => _x('Couldn\'t process request.', 'Social login connect account', 'my-listing'),
        ]);
    }
    public function login()
    {
        if (!is_array($this->user_data) && !isset($this->user_data['email']) || empty($this->user_data['connected_account'])) {
            throw new \Exception('Coudn\'nt pass account');
        }
        $users = get_users(array(
            'meta_key' => $this->user_data['connected_account']['key'],
            'meta_value' => $this->user_data['connected_account']['value'],
            'number' => 1,

        ));
        if (!empty($users)) {
            $this->update_meta($users[0]->ID);
            if ($this->login_existing_user($users[0]->user_login)) {
                return true;
            }
            throw new \Exception('Coud\'nt login');
        }
        if ($user = get_user_by('email', $this->user_data['email'])) {
            $this->update_meta($user->ID);
            update_user_meta($user->ID, $this->user_data['connected_account']['key'], $this->user_data['connected_account']['value']);

            if ($this->login_existing_user($user->user_login)) {
                return true;
            }
            throw new \Exception('Coud\'nt login');
        }

        $args = [];
        $email_parts = explode('@', $this->user_data['email']);
        $args['user_login'] = $email_parts[0];
        $args['user_email'] = $this->user_data['email'];
        $args['user_pass'] = wp_generate_password(16);
        $args['role'] = 'customer';
        if (!empty($this->user_data['first_name'])) {
            $args['first_name'] = $this->user_data['first_name'];
        }
        if (!empty($this->user_data['last_name'])) {
            $args['last_name'] = $this->user_data['last_name'];
        }

        // Edge case: if this user login is taken, append a random id for uniqueness.
        if ($user = get_user_by('login', $args['user_login'])) {
            $args['user_login'] = sprintf('%s.%s', $args['user_login'], bin2hex(openssl_random_pseudo_bytes(2)));
        }
        $user_id = wp_insert_user($args);
        update_user_meta($user_id, $this->user_data['connected_account']['key'], $this->user_data['connected_account']['value']);
        $this->update_meta($user_id);
        update_user_meta($user_id, 'ev_social_picture', $this->name);
        if (!is_wp_error($user_id)) {
            return true;
        } else {
            throw new \Exception('Registration failed');
        }
    }

    public function connect()
    {
        if (!is_array($this->user_data) && !isset($this->user_data['email']) || empty($this->user_data['connected_account']) || !is_user_logged_in()) {
            throw new \Exception('Coudn\'nt pass account');
        }
        $users = get_users(array(
            'meta_key' => $this->user_data['connected_account']['key'],
            'meta_value' => $this->user_data['connected_account']['value'],
            'number' => 1,

        ));
        if (!empty($users)) {
            throw new \Exception('This account is taken by another member');
        }
        update_user_meta(get_current_user_id(), $this->user_data['connected_account']['key'], $this->user_data['connected_account']['value']);
        $this->update_meta(get_current_user_id());
    }
    public function update_meta($user_id)
    {
        if (!is_numeric($user_id) || empty($this->user_data['custom_fields'])) {
            return false;
        }

        foreach ((array) $this->user_data['custom_fields'] as $field_key => $field_value) {
            update_user_meta($user_id, $field_key, $field_value);
        }
    }

    public function login_existing_user($username)
    {
        add_filter('authenticate', [$this, 'allow_proggramatic_login'], 10, 3);
        $user = wp_signon(array(
            'user_login' => $username
        ));
        remove_filter('authenticate', [$this, 'allow_proggramatic_login'], 10, 3);
        if (is_a($user, 'WP_User')) {
            wp_set_current_user($user->ID, $user->user_login);
            if (is_user_logged_in()) {
                return true;
            }
        }
        return false;
    }
    public function allow_proggramatic_login($user, $username, $password)
    {
        return get_user_by('login', $username);
    }
}
