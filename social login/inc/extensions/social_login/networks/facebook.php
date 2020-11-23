<?php

namespace Evelyn\Ext\Social_Login\Networks;

class Facebook extends Network
{
    protected $app_id, $request, $user_data;

    public $option_name = "social_login_ext", $facebook_id = "ev_facebook_id", $name = "facebook", $custom_fields = ['ev_facebook_account_name', 'ev_facebook_account_picture'];

    public function __construct()
    {
        if (!$this->is_enabled()) {
            return;
        }
        $this->app_id = get_option('social_login_ext')['facebook_api'];
        add_action('login_form', function () {
            wp_add_inline_script('social_login_front', $this->login_script(), 'before');
        });
        //add script to amin page
        wp_add_inline_script('theme_script_admin', $this->login_script(), 'before');
    }
    public function is_enabled()
    {
        return get_option($this->option_name)['facebook'] === 'on' && get_option($this->option_name)['facebook_api'];
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
    public function get_picture_url($user_id = null)
    {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        $picture =  get_user_meta($user_id, 'ev_facebook_account_picture', true);
        $picture_url = false;
        if (is_object($picture) && !empty($picture->data) && !empty($picture->data->url)) {
            $picture_url = $picture->data->url;
        }
    }
    public function get_user_data()
    {
        if (empty($this->request['token'])) {
            return;
        }
        $response = wp_remote_get(sprintf('https://graph.facebook.com/v9.0/me?fields=id,first_name,last_name,name,picture,email&access_token=%s', $this->request['token']));
        $data = wp_remote_retrieve_body($response);
        if (is_wp_error($data)) {
            return;
        }
        $this->transform_userdata(json_decode($data));
    }
    public function transform_userdata($data)
    {
        $this->user_data = [];
        if (!is_object($data) || empty($data->id)) {
            return;
        }
        if (!empty($data->first_name)) {
            $this->user_data['first_name'] = $data->first_name;
        }
        if (!empty($data->last_name)) {
            $this->user_data['last_name'] = $data->last_name;
        }
        if (!empty($data->name)) {
            $this->user_data['custom_fields']['ev_facebook_account_name'] = $data->name;
        }
        if (!empty($data->picture)) {
            $this->user_data['custom_fields']['ev_facebook_account_picture'] = $data->picture;
        }
        if (!empty($data->name)) {
            $this->user_data['email'] = $data->email;
        }
        $this->user_data['connected_account'] = array(
            'key' => $this->facebook_id,
            'value' => $data->id
        );
    }
}
