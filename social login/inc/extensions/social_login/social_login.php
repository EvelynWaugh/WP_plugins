<?php

namespace Evelyn\Ext\Social_Login;

class Social_Login
{

    public $networks = [];
    public function __construct()
    {
        add_action('admin_init', [$this, 'register_settings']);
        add_action('wp_ajax_social_login_params', [$this, 'social_save_options']);
        add_action('login_enqueue_scripts', [$this, 'enqueue']);
        add_action('login_form', [$this, "login_template"]);
        add_action('init', [$this, 'initialize']);
    }
    public static function boot()
    {
        return new self();
    }
    public function register_settings()
    {
        $facebook_args = [
            'type' => 'checkbox',
            'id' => 'facebook_init'
        ];
        register_setting('social_login', 'social_login');
        add_settings_section('social_login_id', 'Подключение регистрации через соц.сети', '', 'social_login');
        add_settings_field('facebook_field', 'facebook', [$this, 'display_settings_field'], 'social_login', 'social_login_id', $facebook_args);
    }

    public function display_settings_field($args)
    {
        $o = 'social_login';
        $option_name = get_option('social_login');
        extract($args);
        $checked = $option_name[$id] === 'on' ? 'checked="checked"' : '';
        switch ($type) {
            case 'checkbox':
                echo "<input type='checkbox' id='$id' name='" . $o . "[$id]' $checked />";
                break;
        }
    }
    public function social_save_options()
    {
        $facebook = $_POST['facebook'];
        $google = $_POST['google'];
        $facebook_api = intval($_POST['facebook_api']);
        $google_api = intval($_POST['google_api']);
        $filtered = array_filter($_POST, function ($el) {
            return $el !== 'nonce' && $el !== 'action';
        }, ARRAY_FILTER_USE_KEY);
        check_ajax_referer('social_login_action', 'nonce');
        // check_admin_referer( 'social_login_action', 'social_login_name' );
        // update_option('social_login_ext', array(
        //     "facebook" => !empty($facebook) ? $facebook : 'off',
        //     "google" => !empty($google) ? $google : 'off'
        // ));
        update_option('social_login_ext', $filtered);

        wp_send_json_success(array('post' => $_POST));
    }
    public function enqueue()
    {
        wp_enqueue_script('social_login_front', BOND007_THEME_URL . '/assets2/dist/login.js', ['wp-element'], time(), true);
        wp_localize_script('social_login_front', 'LOGIN', array(
            'nonce' => wp_create_nonce('login_ajax_nonce'),
            'admin_url' => admin_url('admin-ajax.php')
        ));
        wp_enqueue_script(
            'google-platform-js',
            'https://apis.google.com/js/platform.js',
            '',
            null,
            true
        );
    }
    public function login_template()
    {
        echo '<div id="social_login_wrapper"></div>';
    }

    public function initialize()
    {
        $this->setup_networks();

        add_action('wp_ajax_login_endpoint', [$this, 'login_endpoint']);
        add_action('wp_ajax_nopriv_login_endpoint', [$this, 'login_endpoint']);
    }
    public function setup_networks()
    {
        $this->networks['facebook'] = new Networks\Facebook;
        $this->networks['google'] = new Networks\Google;
        $this->networks = array_filter($this->networks, function ($class) {
            return $class->is_enabled();
        });
    }
    public function login_endpoint()
    {
        check_ajax_referer('login_ajax_nonce', 'nonce') || check_ajax_referer('social_login_action', 'nonce');
        $network = $this->networks[$_POST['network']];
        $network->handle_request($_POST);
    }
}
