<?php
class DispletReader extends DispletReader_Woopa
{
	protected $_admin_controller = null;

	protected static $_admin_notices = array();

	protected function _init() {
		require_once $this->_config->include_dir
			. DIRECTORY_SEPARATOR . 'Admin_Controller.php';

		$this->_admin_controller = new DispletReader_Admin_Controller();

		$this->_register_shortcodes();

		$this->_register_nopriv_ajax();

		add_action('admin_init', array(&$this, 'register_tinymce_plugins'));

		add_action('widgets_init', array(&$this, 'widgets_init_callback'));

		add_action('admin_init', array(&$this, 'widgets_admin_callback'));

		add_action('admin_notices', array(__CLASS__, 'admin_notices_callback'));
	}

	public static function admin_notices_callback() {
		foreach (self::$_admin_notices as $notice) {
			echo $notice;
		}
	}

	protected function _register_nopriv_ajax() {
		require_once($this->_config->include_dir
			. DIRECTORY_SEPARATOR . 'Public_Ajax.php');

		add_action('wp_ajax_nopriv_displetreader_sort',
			array('DispletReader_Public_Ajax',
				'sort'));

		add_action('wp_ajax_displetreader_sort',
			array('DispletReader_Public_Ajax',
				'sort'));
	}

	protected function _register_shortcodes() {
		$shortcodes_dir = $this->_config->include_dir
			. DIRECTORY_SEPARATOR
			. 'shortcodes'
			. DIRECTORY_SEPARATOR;

		require_once $this->_config->include_dir
			. DIRECTORY_SEPARATOR . 'Shortcode.php';

		require_once $shortcodes_dir . 'DispletListing_Shortcode.php';
		require_once $shortcodes_dir . 'DispletStats_Shortcode.php';
		require_once $shortcodes_dir . 'DispletFrame_Shortcode.php';
		require_once $shortcodes_dir . 'DispletQSearch_Shortcode.php';

		$dl_shortcode = new DispletListing_Shortcode;
		$ds_shortcode = new DispletStats_Shortcode;
		$df_shortcode = new DispletFrame_Shortcode;
	}

	public function widgets_admin_callback() {
		// hack: load necessary javascript for form dialog on widgets
		$widgets_page = 'widgets.php';
		global $pagenow;

		if ($pagenow === $widgets_page) {
			$css_url = $this->_config->url . '/css/';
			$js_url = $this->_config->url . '/js/';

			wp_enqueue_style('displetreader-jquery-ui',
				$css_url . 'pepper-grinder/jquery-ui-1.8.11.custom.css');

			wp_enqueue_script('displetreader-widget-config',
				$js_url . 'displetreader-widget-config.js',
				array('jquery',
					'jquery-ui-core',
					'jquery-ui-dialog',
					'jquery-ui-tabs',
					'admin-widgets' // not actually a dependency, but should be
									// loaded first so that we can grab the widget title.
				));
		}
	}

	public function register_tinymce_plugins() {
		/*require_once $this->_config->include_dir
			. DIRECTORY_SEPARATOR . 'tinymce'
			. DIRECTORY_SEPARATOR . 'TinyMCE_DispletStats.php';*/

		require_once $this->_config->include_dir
			. DIRECTORY_SEPARATOR . 'tinymce'
			. DIRECTORY_SEPARATOR . 'TinyMCE_DispletListing.php';

		/*$tinymce_stats = new DispletReader_TinyMCE_DispletStats($this->_config->url
			. '/js/mce/DispletStats/tinymce.displetstats.js');

		$tinymce_stats->register();*/

		$tinymce_listing = new DispletReader_TinyMCE_DispletListing($this->_config->url
			. '/js/mce/DispletListing/tinymce.displetlisting.js');

		$tinymce_listing->register();
	}

	public function shortcode_callback($attributes = null) {
		
	}

	public function widgets_init_callback() {
		require_once $this->_config->include_dir
			. DIRECTORY_SEPARATOR
			. 'Widget.php';

		$widgets_dir = $this->_config->include_dir
			. DIRECTORY_SEPARATOR
			. 'widgets'
			. DIRECTORY_SEPARATOR;

		/*
		require_once $widgets_dir . 'DispletListing_Widget.php';
		require_once $widgets_dir . 'DispletSideScroller_Widget.php';
		require_once $widgets_dir . 'DispletStats_Widget.php';

		register_widget('DispletListing_Widget');
		register_widget('DispletSideScroller_Widget');
		register_widget('DispletStats_Widget');
		*/

		self::_widgets_require_hack($widgets_dir, array(
			'DispletListing_Widget',
			'DispletSideScroller_Widget',
			'DispletStats_Widget'
		));
	}

	/**
	 * Temporary loader for widget classes
	 *
	 * Addresses #89, until cause is found.
	 */
	private static function _widgets_require_hack($widgets_dir, array $widgets) {
		foreach ($widgets as $widget) {
			if (!class_exists($widget)) {
				require_once $widgets_dir . $widget . '.php';
				register_widget($widget);
			}
			else {
				if (WP_DEBUG) {
					self::$_admin_notices[] = '<div class="error">DispletReader: WARNING: Attempt to redeclare class ' . $widget . ', please check server configuration.</div>';
				}

				if (DISPLETREADER_DEBUG) {
					error_log('DispletReader: WARNING: Attempt to redeclare class ' . $widget);
				}
			}
		}
	}

	public function template_redirect() {
		
	}

	public function _enqueue_admin() {
		$css_url = $this->_config->url . '/css/';
		$js_url = $this->_config->url . '/js/';

		wp_enqueue_style('displetreader-jquery-ui',
			$css_url . 'pepper-grinder/jquery-ui-1.8.11.custom.css',
			'1.8.11');

		wp_enqueue_style('displetreader-admin',
			$css_url . 'displetreader-admin.css',
			array('displetreader-jquery-ui'),
			'1.0');

		wp_enqueue_script('jquery-validate',
			$js_url . 'jquery-validate/jquery.validate.min.js',
			array('jquery'),
			'1.0', true);
		
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_script('jquery-ui-datepicker');
		
		wp_enqueue_script('displetreader-admin',
			$js_url . 'displetreader-admin.js',
			array('jquery'),
			'1.0', true);
	}
}
