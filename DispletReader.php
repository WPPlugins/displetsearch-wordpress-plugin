<?php

/*
 * Plugin name: DispletReader Legacy
 * Plugin URI: http://displet.com/wordpress-plugins/displetreader-legacy/
 * Description: Uses the Displet 1.0 API (newer version available in Displet RETS/IDX Plugin) to insert real estate listings, statistics, maps, and quick searches into Wordpress pages & widget ready sidebars.
 * Version: 1.5.2.1
 * Author: Displet
 * Author URI: http://displet.com/
 * License: GPL2
 */

/*  Copyright 2011 Displet (email : dev@displet.com)
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License, version 2, as 
 *  published by the Free Software Foundation.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

function DispletReader_activation_callback() {
	$dir = WP_PLUGIN_DIR
		. DIRECTORY_SEPARATOR
		. basename(dirname(__FILE__));
	$include_dir = $dir . DIRECTORY_SEPARATOR . 'include';

	$err_string = '';
	$probs = false;

	if (version_compare(PHP_VERSION, '5.2.1', '<')) {
		$probs = true;
		$err_string .= 'PHP 5.2.1 or newer required.<br>';
	}

	if ($probs) {
		$err_string .= '<br><a href="' . $_SERVER['HTTP_REFERER'] . '">Back</a>';
		deactivate_plugins(WP_PLUGIN_DIR
			. DIRECTORY_SEPARATOR
			. basename(dirname(__FILE__))
			. DIRECTORY_SEPARATOR
			. basename(__FILE__));

		wp_die('DispletReader:<br/>' . $err_string);
	}
}
register_activation_hook(WP_PLUGIN_DIR
	. DIRECTORY_SEPARATOR
	. basename(dirname(__FILE__))
	. DIRECTORY_SEPARATOR
	. basename(__FILE__),
	'DispletReader_activation_callback');

/**
 * Bootstrap (init callback)
 */
function DispletReader_bootstrap() {

	$dir = WP_PLUGIN_DIR
		. DIRECTORY_SEPARATOR
//		. 'displetreader-wordpress-plugin'
//		. DIRECTORY_SEPARATOR
		. basename(dirname(__FILE__));

	$include_dir = $dir . DIRECTORY_SEPARATOR . 'include';

	define('DISPLETREADER_DEBUG', false);

	require_once $include_dir
		. DIRECTORY_SEPARATOR
		. 'woopa'
		. DIRECTORY_SEPARATOR
		. 'Woopa.php';

	require_once $include_dir
		. DIRECTORY_SEPARATOR
		. 'woopa'
		. DIRECTORY_SEPARATOR
		. 'Admin_Ajax.php';

	require_once $include_dir
		. DIRECTORY_SEPARATOR
		. 'DispletReader.php';

	require_once $include_dir
		. DIRECTORY_SEPARATOR
		. 'Config.php';

	require_once $include_dir
		. DIRECTORY_SEPARATOR
		. 'Model.php';

	require_once $include_dir
		. DIRECTORY_SEPARATOR
		. 'View.php';

	require_once $include_dir
		. DIRECTORY_SEPARATOR
		. 'Cache.php';

	require_once $include_dir
		. DIRECTORY_SEPARATOR
		. 'caches'
		. DIRECTORY_SEPARATOR
		. 'Transients_Cache.php';

	require_once $include_dir
		. DIRECTORY_SEPARATOR
		. 'caches'
		. DIRECTORY_SEPARATOR
		. 'Gzip_Transients_Cache.php';

	require_once $include_dir
		. DIRECTORY_SEPARATOR
		. 'ResultSet.php';

	$url = plugins_url(basename(dirname(__FILE__)));
	//$url = plugins_url('displetsearch-wordpress-plugin');

	$config_defaults = array(
		'name' => 'displetreader',
		'settings_name' => 'DispletReader',
		'db_option_name' => 'displetreader',
		'cache' => 'DispletReader_Gzip_Transients_Cache',
		'prefix' => 'DispletReader',
		'settings_role' => 'administrator',
		'dir' => $dir,
		'include_dir' => $include_dir,
		'url' => $url,
		'nothumb_url' => $url . '/images/no_thumb.jpg');

	$config = new DispletReader_Config($config_defaults);

	$displetreader = new DispletReader($config, 'DispletReader_Model');

	if (DISPLETREADER_DEBUG) {
		function displetreader_debug() {
			echo '<pre class="ds_debug">';
			print_r(DispletReader_Woopa_Registry::dump());
			echo '</pre>';
		}
		add_action('admin_footer', 'displetreader_debug');
	}

	return $displetreader;
}
add_action('plugins_loaded', 'DispletReader_bootstrap');