<?php
if (!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) exit();

$plugin_path = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . dirname(WP_UNINSTALL_PLUGIN);

require_once $plugin_path . DIRECTORY_SEPARATOR . 'include'
	.DIRECTORY_SEPARATOR . 'Cache.php';

$settings_name = 'displetreader';
$cache_class_path = $plugin_path . DIRECTORY_SEPARATOR . 'include'
	. DIRECTORY_SEPARATOR . 'caches' . DIRECTORY_SEPARATOR
	. 'Gzip_Transients_Cache.php';
$cache_class_name = 'DispletReader_Gzip_Transients_Cache';

require_once $cache_class_path;

// for backward compatibility with php < 5.3
call_user_func(array($cache_class_name, 'clear'));

delete_option($settings_name);
