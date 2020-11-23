<?php

namespace Evelyn\App;

class FrontEnd
{

    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue'));
    }

    public function enqueue()
    {
        wp_enqueue_script('app-frontend-ev', plugins_url('build/index.js', MAIN_FILE), array('wp-element'), '', true);

        // wp_enqueue_script('app-frontend-ajax', plugins_url('src/ajax.js', MAIN_FILE), array('jquery'), '', true);
    }
}
