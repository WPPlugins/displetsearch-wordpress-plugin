<?php
class DispletReader_Model extends DispletReader_Woopa_Model
{
	protected $_defaults;

	public function __construct() {
		$this->_defaults = array(
			'version' => '1.2.x',
			'enabled' => true,
			'displet_url' => '',
			'rows_per_page' => 10,
			'cache_lifetime' => 86400,
			'default_sort' => 'price_high_to_low',
			'default_orientation' => 'tile',
			'disclaimers_enabled' => false,
			'disclaimer' => '',
			'remove_link' => 'no',
			'price_navigation' => 'yes',
			'property_type_navigation' => 'yes',
			'comments_debug_enabled' => true,
			'displet_pro_search' => true,
			'ondisplet_landing_page' => false,
			'stats' => 'yes',
			'show_pending_count' => 'no',
			'map' => 'yes',
			'map_hide' => 'yes',
			'map_latlong_distance' => 2,
			'include_listing_office' => 'no',
			'displet_quick_search_color' => 'red',
			'include_quick_terms' => 'yes',
			'include_property_type' => 'no',
			'include_price' => 'yes',
			'include_beds_baths' => 'yes',
			'include_keywords' => 'yes',
			'displet_quick_search_propertytypes' => '"House","Condo"',
			'displet_quick_search_minprice' => 100,
			'displet_quick_search_maxprice' => 2000,
			'displet_quick_search_priceincrement' => 100,
			'displet_quick_search_maxbeds' => 6,
			'displet_quick_search_maxbaths' => 6
		);

	}

	protected function _init() {
		// check to see if our settings are okay, restore from defaults if not
		if (!is_array($this->_settings) || count($this->_settings) === 0) {
			$this->_rescue_settings();
		}

		// must be run with every request, for auto updates since wp 3.1
		$this->check_settings();
	}

	protected function _rescue_settings() {
		// try to recover gracefully if our settings get nuked.
		$this->_settings = $this->_defaults;
		update_option(DispletReader_Woopa_Registry::get('config')->db_option_name,
			$this->_settings);
		if (DISPLETREADER_DEBUG) {
			error_log('DispletReader: Bad settings found, restoring from defaults.');
		}
	}

	public function legacy_import() {
		$old_option_names = array(
			'DispletReader_disabled',
			'DispletReader_default_url',
			'DispletReader_rpp',
			'DispletReader_cache_lifetime'
		);

		foreach ($old_option_names as $old_option_name) {
			$option = get_option($old_option_name);

			if ($option) {
				switch ($old_option_name) {
				case 'DispletReader_disabled':
					if ($option !== '0') {
						$this->_settings['enabled'] = false;
					}
					break;

				case 'DispletReader_default_url':
					if ($option != '') {
						$parsed = parse_url($option);
						if (is_array($parsed)) {
							$this->_settings['displet_url'] = $parsed['scheme']
								. '://' . $parsed['host'] . '/';
						}
					}
					break;

				case 'DispletReader_rpp':
					if (is_numeric($option) && $option != 0) {
						$this->_settings['rows_per_page'] = (int) $option;
					}
					break;

				case 'DispletReader_cache_lifetime':
					if (is_numeric($option) && $option != 0) {
						$this->_settings['cache_lifetime'] = (int) $option;
					}
					break;
				}
			}
		}

		/**
		 * @todo uncomment this, delete the old rows when import is known to be good
		 */
		/*
		foreach ($old_option_names as $name) {
			delete_option($name);
		}
		 */
		$this->_save();
	}

	/*
	 * Make sure settings are complete, add defaults if missing
	 */
	public function check_settings() {
		$changed = false;

		if (DISPLETREADER_DEBUG) {
			error_log('DispletReader: Checking settings.');
		}

		foreach ($this->_defaults as $k => $v) {
			if (!isset($this->_settings[$k])) {
				$this->_settings[$k] = $v;
				$changed = true;
			}

			// fix for disclaimers_enabled
			if ($this->_settings['disclaimers_enabled'] == 'false') {
				$this->_settings['disclaimers_enabled'] = false;
			}
		}

		if ($changed) {
			if (DISPLETREADER_DEBUG) {
				error_log('DispletReader: Updating settings. '
					. 'Version change: ' . $this->_settings['version']
					. ' to ' . $this->_defaults['version']);
			}

			$this->_settings['version'] = $this->_defaults['version'];
		}
		
		$thesort = $this->_settings['default_sort'];
		$thedispleturl = $this->_settings['displet_url'];
		$thedispletpro = $this->_settings['displet_pro_search'];
		$thelandingpage = $this->_settings['ondisplet_landing_page'];
		DispletReader_Woopa_Registry::set('thesort', $thesort);
		DispletReader_Woopa_Registry::set('thedispleturl', $thedispleturl);
		DispletReader_Woopa_Registry::set('thedispletpro', $thedispletpro);
		DispletReader_Woopa_Registry::set('thelandingpage', $thelandingpage);
	
		$this->_save();
	}

	public function get_settings() {
		return $this->_settings;
	}

	public function get_url() {
		return $this->_settings['displet_url'];
	}

	public function get_cache_lifetime() {
		return (int) $this->_settings['cache_lifetime'];
	}

	public function get_rpp() {
		$rpp = (int) $this->_settings['rows_per_page'];
		if ($rpp > 0) {
			return $rpp;
		}
		else {
			return 1;
		}
	}

	public function get_default_sort() {
		return $this->_settings['default_sort'];
	}

	public function get_default_orientation() {
		return $this->_settings['default_orientation'];
	}

	public function get_comments_debug_enabled() {
		return $this->_settings['comments_debug_enabled'];
	}

	public function get_pro_search_enabled() {
		return $this->_settings['displet_pro_search'];
	}

	public function get_ondisplet_landing_page() {
		return $this->_settings['ondisplet_landing_page'];
	}

	public function clear_cache() {
		if (DISPLETREADER_DEBUG) {
			error_log('DispletReader: Cache lifetime changed, clearing cache.');
		}

		$cache = $this->_config->cache;

		// not until php 5.3
		//$cache::clear();

		call_user_func(array($cache, 'clear'));
	}
}
