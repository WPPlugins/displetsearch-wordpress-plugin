<?php
class DispletReader_View extends DispletReader_Woopa_View
{
	const LEGACY_SUPPORT = true;
	const LEGACY_VIEWS_DIR = 'Views';

	protected static $_legacy_views = array(
		'displet-listings.php' => array(
			'dir' => 'DispletListing',
			'template' => 'listings.phtml'),
		'displet-sidescroller.php' => array(
			'dir' => 'DispletHView',
			'template' => 'hview.phtml'),
		'displet-stats.php' => array(
			'dir' => 'DispletStats',
			'template' => 'stats.phtml'),
		'displet-listings-widget.php' => array(
			'dir' => 'DispletListing_widget',
			'template' => 'listing_widget.phtml'),
		'displet-sidescroller-widget.php' => array(
			'dir' => 'DispletHView_widget',
			'template' => 'hview_widget.phtml'
		)
	);

	protected static $_legacy_css = array(
		'displet-listings.css' => 'DispletListing.css',
		'displet-sidescroller.css' => 'DispletHListing.css',
		'displet-stats.css' => 'DispletStats.css',
		'displet-listings-widget.css' => 'DispletListing_widget.css',
		'displet-sidescroller-widget.css' => 'DispletHListing.css'
	);

	/* @todo deps for old javascript */
	protected static $_legacy_js = array(
		'legacy_displetreader' => 'DispletReader.js',
		'legacy_navigatorskin' =>'jquery.displetNavigatorSkin.js',
		'legacy_tablepager' => 'jquery.displetTablePager.js',
		'legacy_scrollablesizer' => 'jquery.scrollableSizer.js'
	);

	/**
	 * Get a user-facing template
	 *
	 * This implementation has detection for legacy and theme-based templates.
	 */
	public static function get_template($name, &$model, $model_alias = false) {
		$config = DispletReader_Woopa_Registry::get('config');
		$theme_dir = get_stylesheet_directory();

		// check the current theme's directory
		if ($name == 'custom') {
			$names = array('displet-dynamic.php', 'displet-listings.php', 'displet-tile.php');
			foreach ($names as $name) {
				if (file_exists($theme_dir . DIRECTORY_SEPARATOR . $name)) {
					return self::_render($theme_dir . DIRECTORY_SEPARATOR . $name,
						$model, $model_alias);
				}
			}
		}
		else{
			if (file_exists($theme_dir . DIRECTORY_SEPARATOR . $name)) {
				return self::_render($theme_dir . DIRECTORY_SEPARATOR . $name,
					$model, $model_alias);
			}
		}

		// check the old templates directory for legacy templates
		if (self::LEGACY_SUPPORT) {
			if (isset(self::$_legacy_views[$name])) {
				// check for THEME_ROOT/LEGACY_VIEWS_DIR
				$path = $theme_dir
					. DIRECTORY_SEPARATOR
					. self::LEGACY_VIEWS_DIR
					. DIRECTORY_SEPARATOR
					. self::$_legacy_views[$name]['dir']
					. DIRECTORY_SEPARATOR
					. self::$_legacy_views[$name]['template'];

				if (file_exists($path)) {
					$class = __CLASS__;
					$obj = new $class;
					return $obj->_get_legacy_view($path, $model, $name);
				}

				// check for PLUGIN_ROOT/LEGACY_VIEWS_DIR
				$path = $config->dir
					. DIRECTORY_SEPARATOR
					. self::LEGACY_VIEWS_DIR
					. DIRECTORY_SEPARATOR
					. self::$_legacy_views[$name]['dir']
					. DIRECTORY_SEPARATOR
					. self::$_legacy_views[$name]['template'];

				if (file_exists($path)) {
					// hack: legacy templates expect to be included in an object's scope
					$class = __CLASS__;
					$obj = new $class;
					return $obj->_get_legacy_view($path, $model, $name);
				}
			}
		}

		// load the built-in templates
		return parent::get_template($name, $model, $model_alias);
	}

	public static function enqueue_stylesheet($stylesheet, $slug = false, $deps = array()) {
		$config = DispletReader_Woopa_Registry::get('config');
		$theme_dir = get_stylesheet_directory();
		$theme_url = get_stylesheet_directory_uri();

		if (!$slug) {
			$slug = basename($stylesheet, '.css');
		}

		if (file_exists($theme_dir . DIRECTORY_SEPARATOR . $stylesheet)) {
			wp_enqueue_style($slug, $theme_url . DIRECTORY_SEPARATOR . $stylesheet);
			return;
		}

		if (self::LEGACY_SUPPORT) {
			if (isset(self::$_legacy_css[$stylesheet])) {
				// check THEME_ROOT/LEGACY_VIEWS_DIR
				$url = $theme_url . '/' . self::$_legacy_css[$stylesheet];
				$path = $theme_dir . DIRECTORY_SEPARATOR . self::$_legacy_css[$stylesheet];
				if (file_exists($path)) {
					wp_enqueue_style($slug, $url);
					return;
				}

				// check PLUGIN_ROOT/LEGACY_VIEWS_DIR
				$url = $config->url . '/css/' . self::$_legacy_css[$stylesheet];
				$path = $config->dir . DIRECTORY_SEPARATOR . 'css'
					. DIRECTORY_SEPARATOR . self::$_legacy_css[$stylesheet];
				if (file_exists($path)) {
					wp_enqueue_style($slug, $url);
					return;
				}
			}
		}

		$default_css = $config->url . '/css/' . $stylesheet;

		wp_enqueue_style($slug, $default_css, $deps);		
	}

	public static function enqueue_javascript($javascript, $slug = false, $deps = array()) {
		$config = DispletReader_Woopa_Registry::get('config');
		$theme_dir = get_stylesheet_directory();
		$theme_url = get_stylesheet_directory_uri();

		if (!$slug) {
			$slug = basename($javascript, '.js');
		}

		if (file_exists($theme_dir . DIRECTORY_SEPARATOR . $javascript)) {
			wp_enqueue_script($slug, $theme_url . '/' . $javascript, $deps);
			return;
		}

		if (self::LEGACY_SUPPORT) {
			$has_theme_js = self::_check_legacy_theme_js($config, $theme_dir, $theme_url);

			if (!$has_theme_js) {
				$scripts_exist = true;
				$found_one = false;

				foreach (self::$_legacy_js as $js) {
					if (!file_exists($config->dir . DIRECTORY_SEPARATOR
						. 'js' . DIRECTORY_SEPARATOR . $js)) {
						$scripts_exist = false;
					}
					else {
						$found_one = true;
					}
				}

				if ($scripts_exist && $found_one) {
					if (DISPLETREADER_DEBUG) {
						error_log('DispletReader: Found legacy javascript files, loading.');
					}

					$js_url = $config->url . '/js';

					wp_register_script('displetreader-jquery-tools-scrollable',
						$js_url . '/' . 'jquery-tools' . '/'
						. 'scrollable.min.js' ,
					array('jquery'));

					wp_register_script('displetreader-jquery-tools-scrollable-navigator',
						$js_url . '/' . 'jquery-tools' . '/'
						. 'scrollable.navigator.min.js',
						array('displetreader-jquery-tools-scrollable'));

					$legacy_deps = array();
					foreach (self::$_legacy_js as $k => $v) {
						if ($k !== 'legacy_displetreader') {
							wp_register_script($k, $js_url . '/' . $v, array('jquery'));

							$legacy_deps[] = $k;
						}
					}

					$legacy_deps[] = 'displetreader-jquery-tools-scrollable-navigator';

					wp_enqueue_script('legacy_displetreader',
						$js_url . '/' . self::$_legacy_js['legacy_displetreader'],
						$legacy_deps);
				}
				/*
				if (isset(self::$_legacy_js[$javascript])) {
					$url = $config->url . '/css/' . self::$_legacy_js[$javascript];
					$path = $config->dir . DIRECTORY_SEPARATOR . 'css'
						. DIRECTORY_SEPARATOR . self::$_legacy_js[$javascript];
					if (file_exists($path)) {
						wp_enqueue_script($slug, $url, $deps);
						return;
					}
				}
			 	*/
			}
		}

		$default_js = $config->url . '/js/' . $javascript;

		wp_enqueue_script($slug, $default_js, $deps);
	}

	protected function _get_legacy_view($path, array &$model, $name) {
		if (count($model['results']) > 0) {
			$this->results = self::_xml_to_legacy_array($model['results']);

			if ($name === 'displet-stats.php') {
				$this->count = $model['count'];
				$this->price_range = $model['price_range'];
				$this->size_range = $model['size_range'];
			}
		}
		else {
			$this->count = 0;
			$this->results = new stdClass;
			$this->price_range = array('min' => 0, 'max' => 0);
			$this->size_range = array('min' => 0, 'max' => 0);
		}

		// @todo add other properties as required
		ob_start();
		include $path;
		$output = ob_get_contents();
		ob_end_clean();

		// necessary for legacy support for widgets
		if ($name === 'displet-listings-widget.php') {
			$output = '<div class="Widget_DispletListing">' . $output . '</div>';
		}

		return $output;
	}

	private static function _xml_to_legacy_array(SimpleXMLElement $simple_xml) {
		$url = DispletReader_Woopa_Registry::get('config')->url;
		$array = array();
		$domdocument = new DOMDocument;
		$domdocument->loadXML($simple_xml->asXML());

		$residentials = $domdocument->getElementsByTagName('residential');

		$i = 0;
		if ($residentials) {
			foreach ($residentials as $residential) {
				$children = $residential->childNodes;
				foreach ($children as $child) {
					$name = $child->nodeName;
					$value = $child->nodeValue;
					$array[$i][$name] = $value;
				}

				$thumbs = $residential->getElementsByTagName('thumb');
				if ($thumbs->length > 0) {
					$thumbs_array = array();
					foreach ($thumbs as $thumb) {
						$thumbs_array[] = $thumb->nodeValue;
					}
					sort($thumbs_array);
					$array[$i]['images'] = $thumbs_array;
				}
				else {
					$thumbs_array = array();
					$thumbs_array[] = $url . '/images/no_thumb.jpg';
					$array[$i]['images'] = $thumbs_array;
				}
				$i++;
			}
		}

		return $array;
	}

	private static function _check_legacy_theme_js($config, $theme_dir, $theme_url) {
		$scripts_exist = true;
		$found_one = false;

		foreach (self::$_legacy_js as $js) {
			if (!file_exists($theme_dir . DIRECTORY_SEPARATOR . $js)) {
				$scripts_exist = false;
			}
			else {
				$found_one = true;
			}
		}

		if ($scripts_exist && $found_one) {
			if (DISPLETREADER_DEBUG) {
				error_log('Found legacy javascript files, loading.');
			}

			$js_url = $config->url . '/js';

			wp_register_script('displetreader-jquery-tools-scrollable',
				$js_url . '/' . 'jquery-tools' . '/'
				. 'scrollable.min.js' ,
				array('jquery'));

			wp_register_script('displetreader-jquery-tools-scrollable-navigator',
				$js_url . '/' . 'jquery-tools' . '/'
				. 'scrollable.navigator.min.js',
				array('displetreader-jquery-tools-scrollable'));

			$legacy_deps = array();
			foreach (self::$_legacy_js as $k => $v) {
				if ($k !== 'legacy_displetreader') {
					wp_register_script($k, $theme_url . '/' . $v, array('jquery'));

					$legacy_deps[] = $k;
				}
			}

			$legacy_deps[] = 'displetreader-jquery-tools-scrollable-navigator';

			wp_enqueue_script('legacy_displetreader',
				$theme_url . '/' . self::$_legacy_js['legacy_displetreader'], $legacy_deps);

			return true;
		}

	}
}
