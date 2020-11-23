<?php


namespace Evelyn\Admin;

class AdminPages
{

    public function __construct()
    {
        add_action('admin_menu', array($this, 'init'));
    }

    public function init()
    {
        add_menu_page('Private Messaging', 'PrivateMessaging', 'manage_options', 'private_ev_plugin', array($this, 'addMenuPage'));

        add_submenu_page('private_ev_plugin', 'System Status', 'System Status', 'manage_options', 'status_ev_plugin', array($this, 'addSubPage'));
        add_submenu_page('private_ev_plugin', 'test', 'test', 'manage_options', 'test_ev_plugin', array($this, 'addSubPage_2'));
    }

    public function addMenuPage()
    {
        include_once MAIN_PATH . 'admin/templates/main-page.php';
    }

    public function addSubPage()
    {
        include_once MAIN_PATH . 'admin/templates/sub-page-status.php';
    }
    public function addSubPage_2()
    {
        include_once MAIN_PATH . 'admin/templates/ev-settings.php';
    }
}
