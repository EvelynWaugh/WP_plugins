<?php

?>
<div class="wrap">
    <form action="options.php" method="post">
        <?php
        settings_errors();
        settings_fields('social_login');
        do_settings_sections('social_login');
        submit_button();
        ?>
    </form>
    <div class="connected_accounts">
        <?php
        $picture =  get_user_meta(get_current_user_id(), 'ev_facebook_account_picture', true);
        $picture_url = false;
        if (is_object($picture) && !empty($picture->data) && !empty($picture->data->url)) {
            $picture_url = $picture->data->url;
        }
        $name = get_user_meta(get_current_user_id(), 'ev_facebook_account_name', true);

        ?>
        <div class="facebook_name">
            <h3><?php echo $name; ?></h3>
        </div>
        <div class="facebook_pic">
            <img src="<?php echo $picture_url ?>" alt="">
        </div>
    </div>
    <div id="social_login"></div>
</div>