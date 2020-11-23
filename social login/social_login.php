<?php
if(!defined(ABSPATH)) {
die;
}

if (!defined('BOND007_THEME_DIR')) {
	define('BOND007_THEME_DIR', plugin_dir_path(__FILE__));
}
if (!defined('BOND007_THEME_URL')) {
	define('BOND007_THEME_URL', plugin_dir_url(__FILE__));
}

if (!defined('BOND007_ASSETS_DIR')) {
	define('BOND007_ASSETS_DIR', BOND007_THEME_DIR . '/assets');
}

if (!defined('BOND007_ENV')) {
	define('BOND007_ENV', 'production');
}





require_once plugin_dir_path(__FILE__) . '/inc/autoload.php';




