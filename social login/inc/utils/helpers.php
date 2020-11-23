<?php

namespace Evelyn\Utils;

class Helpers
{

    protected static $_instance = null;


    public function __construct()
    {
    }
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function new_admin_page($type = 'menu', $args = [])
    {
        if (in_array($type, ['menu', 'submenu'])) {
            call_user_func_array('add_' . $type . '_page', $args);
        }
    }

    public function register_rest_admin()
    {
    }
}
