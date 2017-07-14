<?php
class DispletReader_Admin_Controller
{
	protected $_config;
	protected $_model;
	protected $_menu_helper;

	protected $_settings_fields = array(
		'enabled' => array(
			'id' => 'enabled',
			'label' => 'Status'
		),
		'displet_url' => array('id' => 'displet_url',
			'label' => 'Displet URL'
		),
		'rows_per_page' => array('id' => 'rows_per_page',
			'label' => 'Default Listings Per Page'
		),
		'cache_lifetime' => array('id' => 'cache_lifetime',
			'label' => 'Default Cache Lifetime'
		),
		'default_sort' => array('id' => 'default_sort',
			'label' => 'Default Sort'
		),
		'stats' => array('id' => 'stats',
			'label' => 'Default Stats Include'
		),
		'show_pending_count' => array('id' => 'show_pending_count',
			'label' => 'Show Pending Listings Count'
		),
		'include_listing_office' => array('id' => 'include_listing_office',
			'label' => 'Include Listing Office'
		),
		'map' => array('id' => 'map',
			'label' => 'Include Map by Default?'
		),
		'map_hide' => array('id' => 'map_hide',
			'label' => 'Hide Map by Default?'
		),
		'map_height' => array('id' => 'map_height',
			'label' => 'Default Map Height'
		),
		'map_width' => array('id' => 'map_width',
			'label' => 'Default Map Width'
		),
		'map_latlong_distance' => array('id' => 'map_latlong_distance',
			'label' => 'Max Latitudinal/Longitudinal Variance'
		),
		'displet_quick_search_color' => array('id' => 'displet_quick_search_color',
			'label' => 'Quick Search Color'
		),
		'include_quick_terms' => array('id' => 'include_quick_terms',
			'label' => 'Quick Search: Include Quick Terms?'
		),
		'include_property_type' => array('id' => 'include_property_type',
			'label' => 'Quick Search: Include Property Type?'
		),
		'include_beds_baths' => array('id' => 'include_beds_baths',
			'label' => 'Quick Search: Include Beds/Baths?'
		),
		'include_price' => array('id' => 'include_price',
			'label' => 'Quick Search: Include Price?'
		),
		'include_keywords' => array('id' => 'include_keywords',
			'label' => 'Quick Search: Include Keywords?'
		),
		'displet_quick_search_propertytypes' => array('id' => 'displet_quick_search_propertytypes',
			'label' => 'Quick Search Property Types'
		),
		'displet_quick_search_minprice' => array('id' => 'displet_quick_search_minprice',
			'label' => 'Quick Search Min Price'
		),
		'displet_quick_search_maxprice' => array('id' => 'displet_quick_search_maxprice',
			'label' => 'Quick Search Max Price'
		),
		'displet_quick_search_priceincrement' => array('id' => 'displet_quick_search_priceincrement',
			'label' => 'Quick Search Price Increment'
		),
		'displet_quick_search_maxbeds' => array('id' => 'displet_quick_search_maxbeds',
			'label' => 'Quick Search Max Beds'
		),
		'displet_quick_search_maxbaths' => array('id' => 'displet_quick_search_maxbaths',
			'label' => 'Quick Search Max Baths'
		),
		'default_orientation' => array('id' => 'default_orientation',
			'label' => 'Default Listings Display'
		),
		'disclaimers_enabled' => array('id' => 'disclaimers_enabled',
			'label' => 'Disclaimers'
		),
		'disclaimer' => array('id' => 'disclaimer',
			'label' => 'Disclaimer'
		),
		'remove_link' => array('id' => 'remove_link',
			'label' => 'Remove Link'
		),
		'price_navigation' => array('id' => 'price_navigation',
			'label' => 'Price Navigation'
		),
		'property_type_navigation' => array('id' => 'property_type_navigation',
			'label' => 'Property Type Navigation'
		),
		'comments_debug_enabled' => array('id' => 'comments_debug_enabled',
			'label' => 'Debug in HTML comments'
		),
		'displet_pro_search' => array('id' => 'displet_pro_search',
			'label' => 'Search Type'
		),
		'ondisplet_landing_page' => array('id' => 'ondisplet_landing_page',
			'label' => 'Search Landing Page'
		)
	);

	public function __construct() {
		$this->_config = DispletReader_Woopa_Registry::get('config');
		$this->_model = DispletReader_Woopa_Registry::get('model');

		require_once($this->_config->include_dir
			. DIRECTORY_SEPARATOR . 'Admin_Menu_Helper.php');

		$this->_menu_helper = new DispletReader_Admin_Menu_Helper;
		$this->_init();
	}

	protected function _init() {
		add_action('admin_init', array(&$this, 'register_settings'));
	}

	public function register_settings() {
		$option_name = $this->_config->db_option_name;
		$options_page = $this->_config->admin_page_slug;
		$settings_name = $this->_config->settings_name;

		register_setting($option_name,
			$option_name,
			array(&$this, 'filter'));
		add_settings_section($option_name . '-main',
			$settings_name . ' Options',
			array(__CLASS__, 'settings_section_callback'),
			$options_page);

		foreach ($this->_settings_fields as $field) {
			add_settings_field('displetreader-' . $field['id'],
				$field['label'],
				array(&$this->_menu_helper, $field['id']),
				$options_page,
				$option_name . '-main');
		}
	}

	public static function settings_section_callback () {
		echo 'foo';
	}

	public function filter(array $input) {
		$names = array_keys($this->_settings_fields);
		$whitelisted = array();

		foreach ($input as $k => $v) {
			if (in_array($k, $names)) {
				$whitelisted[$k] = $v;
			}
		}

		if (count($whitelisted) === 0) {
			return false;
		}

		$settings = $this->_model->get_settings();

		foreach ($whitelisted as $k => $v) {
			switch ($k) {
			case 'enabled':
				$enabled = filter_var($v, FILTER_VALIDATE_BOOLEAN,
					array('flags' => FILTER_NULL_ON_FAILURE));
				if ($enabled !== null) {
					$settings['enabled'] = (bool) $v;
				}
				break;

			case 'displet_url':
				if (empty($v)) {
					$settings['displet_url'] = '';
					continue;
				}
				$scheme_regex = '/^[[:alpha:]]+:\/\//';
				$rfc_3986_regex = '~^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?~';

				$url = $v;
				if (!preg_match($scheme_regex, $url)) {
					$url = 'http://' . $url;
				}

				$url = trailingslashit($url);

				if (preg_match($rfc_3986_regex, $url)) {
					$settings['displet_url'] = $url;
				}
				break;

			case 'rows_per_page':
				if (is_numeric($v)) {
					if ($v < 51 && $v > 0) {
						$settings['rows_per_page'] = (int) $v;
					}
					elseif ($v > 50) {
						$settings['rows_per_page'] = 50;
					}
					else{
						$settings['rows_per_page'] = 10;
					}
					
				}
				break;

			case 'cache_lifetime':
				if (is_numeric($v)) {

					if ($v != $this->_model->get_cache_lifetime()) {
						$this->_model->clear_cache();
					}

					$settings['cache_lifetime'] = (int) $v;
				}
				break;

			case 'default_sort':
				if ($v === 'price_low_to_high' || $v === 'price_high_to_low' || $v === 'list_date_desc') {
					$settings['default_sort'] = $v;
				}
				break;

			case 'stats':
				if ($v === 'yes' || $v === 'no') {
					$settings['stats'] = $v;
				}
				break;

			case 'show_pending_count':
				if ($v === 'yes' || $v === 'no') {
					$settings['show_pending_count'] = $v;
				}
				break;

			case 'remove_link':
				if ($v === 'yes' || $v === 'no') {
					$settings['remove_link'] = $v;
				}
				break;

			case 'price_navigation':
				if ($v === 'yes' || $v === 'no') {
					$settings['price_navigation'] = $v;
				}
				break;

			case 'property_type_navigation':
				if ($v === 'yes' || $v === 'no') {
					$settings['property_type_navigation'] = $v;
				}
				break;
				
			case 'include_listing_office':
				if ($v === 'yes' || $v === 'no') {
					$settings['include_listing_office'] = $v;
				}
				break;

			case 'map':
				if ($v === 'yes' || $v === 'no') {
					$settings['map'] = $v;
				}
				break;

			case 'map_hide':
				if ($v === 'yes' || $v === 'no') {
					$settings['map_hide'] = $v;
				}
				break;

			case 'map_height':
				if (is_numeric($v)) {
					$settings['map_height'] = (int) $v;
				}
				else {
					$settings['map_height'] = '';
				}
				break;

			case 'map_width':
				if (is_numeric($v)) {
					$settings['map_width'] = (int) $v;
				}
				else {
					$settings['map_width'] = '';
				}
				break;
				
			case 'map_latlong_distance':
				if (is_numeric($v)) {
					$settings['map_latlong_distance'] = (int) $v;
				}
				else {
					$settings['map_latlong_distance'] = '';
				}
				break;

			case 'displet_quick_search_color':
				if ($v === 'black' || $v === 'red' || $v === 'green' || $v === 'blue' || $v === 'yellow' || $v === 'brown') {
					$settings['displet_quick_search_color'] = $v;
				}
				break;

			case 'displet_quick_search_propertytypes':
				$settings['displet_quick_search_propertytypes'] = $v;
				break;
				
			case 'include_quick_terms':
				if ($v === 'yes' || $v === 'no') {
					$settings['include_quick_terms'] = $v;
				}
				break;
				
			case 'include_property_type':
				if ($v === 'yes' || $v === 'no') {
					$settings['include_property_type'] = $v;
				}
				break;
				
			case 'include_price':
				if ($v === 'yes' || $v === 'no') {
					$settings['include_price'] = $v;
				}
				break;
				
			case 'include_beds_baths':
				if ($v === 'yes' || $v === 'no') {
					$settings['include_beds_baths'] = $v;
				}
				break;
				
			case 'include_keywords':
				if ($v === 'yes' || $v === 'no') {
					$settings['include_keywords'] = $v;
				}
				break;

			case 'displet_quick_search_minprice':
				if (is_numeric($v)) {
					$settings['displet_quick_search_minprice'] = (int) $v;
				}
				else {
					$settings['displet_quick_search_minprice'] = '';
				}
				break;

			case 'displet_quick_search_maxprice':
				if (is_numeric($v)) {
					$settings['displet_quick_search_maxprice'] = (int) $v;
				}
				else {
					$settings['displet_quick_search_maxprice'] = '';
				}
				break;

			case 'displet_quick_search_priceincrement':
				if (is_numeric($v)) {
					$settings['displet_quick_search_priceincrement'] = (int) $v;
				}
				else {
					$settings['displet_quick_search_priceincrement'] = '';
				}
				break;

			case 'displet_quick_search_maxbeds':
				if (is_numeric($v)) {
					$settings['displet_quick_search_maxbeds'] = (int) $v;
				}
				else {
					$settings['displet_quick_search_maxbeds'] = '';
				}
				break;

			case 'displet_quick_search_maxbaths':
				if (is_numeric($v)) {
					$settings['displet_quick_search_maxbaths'] = (int) $v;
				}
				else {
					$settings['displet_quick_search_maxbaths'] = '';
				}
				break;

			case 'default_orientation':
				if ($v === 'vertical' || $v === 'custom' || $v === 'tile') {
					$settings['default_orientation'] = $v;
				}
				break;

			case 'disclaimers_enabled':
				//if ($v == 'true' || $v == 'false') {
				//	$settings['disclaimers_enabled'] = $v;
				//}

				$disclaimers_enabled = filter_var($v, FILTER_VALIDATE_BOOLEAN,
					array('flags' => FILTER_NULL_ON_FAILURE));
				if ($disclaimers_enabled !== null) {
					$settings['disclaimers_enabled'] = (bool) $v;
				}
				break;

			case 'disclaimer':
				$settings['disclaimer'] = wp_kses_post($v);
				break;

			case 'comments_debug_enabled':
				$comments_debug_enabled = filter_var($v, FILTER_VALIDATE_BOOLEAN,
					array('flags' => FILTER_NULL_ON_FAILURE));
				if ($comments_debug_enabled !== null) {
					$settings['comments_debug_enabled'] = (bool) $v;
				}
				break;
			
			case 'displet_pro_search':
				$displet_pro_search = filter_var($v, FILTER_VALIDATE_BOOLEAN,
					array('flags' => FILTER_NULL_ON_FAILURE));
				if ($displet_pro_search !== null) {
					$settings['displet_pro_search'] = (bool) $v;
				}
				break;
				
			case 'ondisplet_landing_page':
				$check_page = get_post($v);
				if (!empty($check_page)) {
					$settings['ondisplet_landing_page'] = $v;
				}
				break;
			}
		}

		return $settings;
	}
}
