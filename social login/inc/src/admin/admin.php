<?php

namespace Evelyn\Src\Admin;

class Admin
{
    protected $add_ip;
    public function __construct()
    {
        $this->app_id = get_option('social_login_ext')['facebook_api'];
        add_action('admin_enqueue_scripts', [$this, 'enqueue']);
        add_action('admin_menu', array($this, 'admin_menu'));
    }
    public static function boot()
    {
        return new self();
    }

    public function admin_menu()
    {
        global $menu;
        wsjr()->new_admin_page('menu', array(
            'EV_Theme Options',
            'Evelyn Theme Options',
            'manage_options',
            'ev_theme_options',
            function () {
                echo '<h1>MAIN</h1>';
            }
        ));
        wsjr()->new_admin_page('submenu', array(
            'ev_theme_options',
            'Social Login',
            'Social Login',
            'manage_options',
            'social_login',
            // call_user_func_array([$this, 'admin_templates'], ['social'])
            // call_user_func([$this, 'admin_templates'], 'social')
            //    array($this, 'admin_templates')
            function () {
                include BOND007_THEME_DIR . '/templates2/admin/social_login.php';
            }
        ));

        add_option('social_login_ext', []);
    }

    public function admin_templates($name)
    {
        switch ($name) {
            case 'social':
                include BOND007_THEME_DIR . '/templates2/admin/social_login.php';
                break;
            default:
                include BOND007_THEME_DIR . '/templates2/admin/social_login.php';
        }
    }

    public function enqueue($hook)
    {
        // wp_enqueue_script();

        wp_register_script('theme_script_admin', BOND007_THEME_URL . '/assets2/dist/admin.js', array('wp-element'), time(), true);
        wp_enqueue_script('theme_script_admin');
        wp_localize_script('theme_script_admin', 'OPTIONS', array(
            'social_login' => get_option('social_login_ext'),
            'nonce' => wp_create_nonce('social_login_action'),

        ));
        wp_localize_script('theme_script_admin', 'EV_PARAMS', array(
            'admin_url' => admin_url('admin-ajax.php')
        ));

        wp_add_inline_script('theme_script_admin', $this->login_script(), 'before');
    }
    public function login_script()
    {
        ob_start();
?>
        <script>
            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s);
                js.id = id;
                js.async = true;
                js.defer = true;
                js.src = "https://connect.facebook.net/en_US/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));

            window.fbAsyncInit = function() {
                FB.init({
                    appId: '<?php echo esc_attr($this->app_id) ?>',
                    cookie: true,
                    xfbml: true,
                    version: 'v9.0'
                });
            };
        </script>
<?php
        return  $script = ob_get_clean();
    }
}
