<?php

namespace Evelyn;

class App
{

    protected static $_instance = null;
    private $classes = [];
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

    public function register(array $classes)
    {
        foreach ($classes as $key => $class) {
            $this->classes[$key] = $class;
        }
    }

    public function boot(array $classes)
    {
        foreach ($classes as $class) {
            call_user_func([$class, 'boot']);
        }
    }
    public function __call($method, $params)
    {
        if (isset($this->classes[$method])) {
            return $this->classes[$method];
        }
        return null;
    }
}
