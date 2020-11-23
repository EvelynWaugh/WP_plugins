<?php

spl_autoload_register(function ($className) {


    $path = explode('\\', $className);
    if ($path[0] !== 'Evelyn') {
        return;
    }
    $path[0] = 'Includes2';
    if ($path[1] === 'Ext') {
        $path[1] = 'Extensions';
    }
    $path = array_map(function ($part) {
        return strtolower($part);
    }, $path);
    $path = implode('/', $path) . '.php';
    if (locate_template($path)) {
        require_once locate_template($path);
        // echo locate_template($path);
    }
});
require_once locate_template('includes2/util.php');
require_once locate_template('includes2/init.php');
// $admin = new \Evelyn\Src\Admin\Admin();

// $admin = new Admin();
