<?php
/**
 * @version 0.1.21
 * Wordpress object oriented plugin adapter.
 * (c) 2010 Scott Drake, licensed under the GPL v3 or later (see license.txt)
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Model.php';

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Config.php';

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Registry.php';

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'View.php';

/**
 * Main DispletReader_Woopa abstract class.
 *
 * @defgroup DispletReader_Woopa
 */
abstract class DispletReader_Woopa 
{
	protected $_config; /**< DispletReader_Woopa_Config instance */
	protected $_model; /**< DispletReader_Woopa_Model instance */

	/**
	 * @param $config A DispletReader_Woopa_Config derived instance.
	 * @param $model_class A string, name of the Model class.
	 */
	public function __construct(DispletReader_Woopa_Config $config, $model_class) {
		DispletReader_Woopa_Registry::set('config', $config);

		try {
			if (class_exists($model_class)) {
				$model = new $model_class;
			}
			else {
				throw new Exception('\'' . $model_class . '\' not defined.');
			}

			if (!($model instanceof DispletReader_Woopa_Model)) {
				throw new Exception('Not a DispletReader_Woopa_Model.');
			}

			DispletReader_Woopa_Registry::set('model', $model);

			$model->init();

			$this->_config = $config;
			$this->_model = $model;

			add_action('admin_menu', array($this, 'admin_menu_callback'));
			add_action('admin_init', array($this, 'admin_init_callback'));
			add_action('wp_ajax_' . $this->_config->name, array($this, 'admin_ajax_callback'));
			add_action('template_redirect', array($this, 'template_redirect'));

			$this->_init();
		}
		catch (Exception $e) {
			echo 'Couldn\'t instantiate model. ' . $e->getMessage();
		}

	}

	/**
	 * Init template method.
	 *
	 * Override for any configuration necessary after construction.
	 */
	protected function _init() {
		
	}

	/**
	 * admin_menu WP callback
	 */
	public function admin_menu_callback() {
		/*add_menu_page($this->_config->name . ' Options',
			$this->_config->settings_name,
			$this->_config->settings_role,
			$this->_config->admin_page_slug,
			array($this, 'options_page_callback'),
			plugins_url('displetreader-wordpress-plugin/images/displet.png'),
			76
			);*/
		add_menu_page('Displet Tools',
			'Displet Tools',
			'administrator',
			'displettools-uid-slug',
			array($this, 'options_page_callback'),
			plugins_url('displetsearch-wordpress-plugin/images/displet.png'),
			76
			);
		add_submenu_page('displettools-uid-slug',
			'DispletReader',
			'DispletReader',
			'administrator',
			'displetreader-uid-slug-submenu',
			array($this, 'options_page_callback')
			);			
		add_submenu_page('displettools-uid-slug',
			'Displet IDX',
			'Displet IDX',
			'administrator',
			'displetidx-uid-slug-submenu',
			array($this, 'idx_options_page_callback')
			);
		remove_submenu_page('displettools-uid-slug', 'displettools-uid-slug');
	}

	/**
	 * add_options_page callback.
	 */
	public function options_page_callback() {
		$settings = $this->_model->dump();
		$config = $this->_config;

		include $this->_config->admin_template_dir
			. DIRECTORY_SEPARATOR
			. 'options_page.phtml';
	}
	
	public function idx_options_page_callback() {
		$settings = $this->_model->dump();
		$config = $this->_config;

		include $this->_config->admin_template_dir
			. DIRECTORY_SEPARATOR
			. 'idx_options_page.phtml';
	}

	/**
	 * admin_init callback
	 *
	 * @todo Admin_Ajax no longer exists, see admin_ajax_callback()
	 */
	public function admin_init_callback() {
		global $plugin_page;

		if ($plugin_page == $this->_config->admin_page_slug) {
			$this->_enqueue_admin();

			// this is a hack, code is duplicated in admin_ajax_callback().
			// here only for settings import for now, which is the only
			// non-idempotent action not easily done with ajax.
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				require_once $this->_config->include_dir
					. DIRECTORY_SEPARATOR . 'Admin_Ajax.php';
				Admin_Ajax::process_post($_POST);
			}
		}
	}

	/**
	 * Admin ajax callback
	 */
	public function admin_ajax_callback() {
		require_once $this->_config->include_dir . DIRECTORY_SEPARATOR . 'Admin_Ajax.php';

		$classname = $this->_config->prefix . '_Admin_Ajax';

		$o = new $classname;

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$o->process_post($_POST);
		}
		else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			$o->process_get($_GET);
		}

		die();	
	}

	/**
	 * Shortcode callback
	 *
	 * There is a hack in DispletReader_Woopa::template_redirect that uses
	 * DispletReader_Woopa::_sniff_shortcode to determine whether our shortcode
	 * is in the page.
	 */
	public function shortcode_callback() {
		
	}

	/**
	 * Template redirect callback
	 *
	 * Enqueues scripts and styles if DispletReader_Woopa::_sniff_shortcode returns
	 * true.
	 */
	public function template_redirect() {
		if ($this->_sniff_shortcode()) {
			wp_enqueue_style($this->_config->prefix . '_css',
				$this->_config->url . '/css/style.css');

			if (!file_exists($this->_config->dir
				. DIRECTORY_SEPARATOR
				. 'css'
				. DIRECTORY_SEPARATOR . 'jquery-ui.css')) {
				wp_enqueue_style('jquery-ui-base',
					'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/vader/jquery-ui.css');
			}
			else {
				wp_enqueue_style('jquery-ui-base',
					$this->_config->url . '/css/jquery-ui.css');
			}

			wp_enqueue_script($this->_config->prefix . ' jquery-ui-1.8.1',
				$this->_config->url . '/js/jquery-ui.min.js',
				array('jquery'));

			wp_enqueue_script($this->_config->prefix,
				$this->_config->url . '/js/' . $this->_config->prefix . '.js',
				array('jquery', $this->_config->prefix . ' jquery-ui-1.8.1'));

		}
	}

	/**
	 * Check content for our shortcode
	 *
	 * Hack necessary to put styles and script in head only when the shortcode is present.
	 */
	protected function _sniff_shortcode() {
		$regex = '/' . get_shortcode_regex() . '/s';
		global $posts;

		if (isset($posts[0])) {
			preg_match($regex, $posts[0]->post_content, $matches);
			if (is_array($matches)
				&& isset($matches[2])
				&& $matches[2] == $this->_config->name) {
				return true;
			}
		}
	}

	/**
	 * Enqueue admin stylesheets and scripts
	 */
	protected function _enqueue_admin() {
		wp_enqueue_script('jquery');
		wp_enqueue_script($this->_config->name . '-jquery-ui-1.8.1',
			$this->_config->url . '/js/jquery-ui.min.js',
			array('jquery'));

		wp_enqueue_script($this->_config->name . '-admin',
			$this->_config->url . '/js/admin.js',
			array($this->_config->name . '-jquery-ui-1.8.1'));

		wp_enqueue_style('jquery-ui-base',
			'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/cupertino/jquery-ui.css'
		);

		wp_enqueue_style($this->_config->name . '-admin',
			$this->_config->url . '/css/admin.css');
	}
}

//add_action('init',
//	create_function('', '$displetsearch = new DispletSearch;'));

/*
function ds_debug() {
	global $wp_actions;
	global $wp_filter;

	echo '<pre class="ds_debug">';
	echo '$wp_actions:<br/>';
	print_r($wp_actions);
	echo '$wp_filter:<br/>';
	print_r($wp_filter);
	echo '</pre>';
}
add_action('admin_footer', 'ds_debug');
 */
