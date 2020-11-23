<?php


/**
 * Plugin Name: Private Messaging
 * Author: Incognito Messaging Group
 */

if (!defined('ABSPATH')) exit;

define('MAIN_FILE', __FILE__);
define('MAIN_URL', plugin_dir_url(__FILE__));
define('MAIN_PATH', plugin_dir_path(__FILE__));
include_once MAIN_PATH . 'app/helpers.php';
require_once MAIN_PATH . 'app/class-private-install.php';
require_once MAIN_PATH . 'app/class-frontend.php';
require_once MAIN_PATH . 'admin/class-admin-pages.php';
require_once MAIN_PATH . 'app/class-shcode.php';
require_once MAIN_PATH . 'app/class-ajax.php';


function privateMessagingInit()
{

    new Evelyn\App\PrivateInstall();
    new Evelyn\Admin\AdminPages();
    new Evelyn\App\FrontEnd();
    $shorcodes =  new Evelyn\App\PrivateShortcodes();
    $shorcodes->initShortcode();
    new Evelyn\App\PrivateAjax();
}
privateMessagingInit();
