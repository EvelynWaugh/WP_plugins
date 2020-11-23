<?php

namespace Evelyn\Ext\Social_Login\Networks;

class Google extends Network
{
    protected $app_id, $request, $user_data;

    public $option_name = "social_login_ext", $google_id = "ev_google_id", $name = "google", $custom_fields = ['ev_google_account_name', 'ev_google_account_picture'];

    public function __construct()
    {
        if (!$this->is_enabled()) {
            return;
        }
        $this->app_id = get_option('social_login_ext')['google_api'];
        add_action('login_form', function () {
            wp_enqueue_script(
                'google-platform-js',
                'https://apis.google.com/js/platform.js?onload=ev_google_init',
                '',
                null,
                true
            );
        }, 50);
        add_action('login_enqueue_scripts', function () { ?>
            <meta name="google-signin-client_id" content="<?php echo esc_attr($this->app_id) ?>">
<?php });
    }
    public function is_enabled()
    {
        return get_option($this->option_name)['google'] === 'on' && get_option($this->option_name)['google_api'];
    }
    public function get_user_data()
    {
        if (empty($this->request['token'])) {
            return;
        }
        $response = wp_remote_get(sprintf('https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=%s', $this->request['token']));
        $data = wp_remote_retrieve_body($response);
        if (is_wp_error($data)) {
            return;
        }
        $this->transform_userdata(json_decode($data));
    }

    public function get_picture_url($user_id = null)
    {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        return get_user_meta($user_id, 'ev_google_account_picture', true);
    }

    public function transform_userdata($data)
    {
        $this->user_data = [];

        if (!is_object($data) || empty($data->aud) || $data->aud !== $this->app_id || empty($data->email)) {
            return false;
        }

        $this->user_data['email'] = $data->email;

        if (!empty($data->given_name)) {
            $this->user_data['first_name'] = $data->given_name;
        }

        if (!empty($data->family_name)) {
            $this->user_data['last_name'] = $data->family_name;
        }

        // Used to tell if this account has already been connected to the user.
        $this->user_data['connected_account'] = [
            'key' => $this->google_id,
            'value' => $data->email,
        ];

        $this->user_data['custom_fields'] = [];

        if (!empty($data->name)) {
            $this->user_data['custom_fields']['ev_google_account_name'] = $data->name;
        }

        if (!empty($data->picture)) {
            $this->user_data['custom_fields']['ev_google_account_picture'] = $data->picture;
        }
    }
}
