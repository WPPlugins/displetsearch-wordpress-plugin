<?php
/**
 * Model layer
 *
 * Handles wordpress's settings api. Uses a multi array approach for now.
 *
 * @ingroup DispletReader_Woopa
 */
abstract class DispletReader_Woopa_Model
{
	protected $_settings = array();
	protected $_defaults = array();
	protected $_config;
	protected $_db_option_name;

	/**
	 * Initialization method.
	 *
	 * Called by the main object.
	 */
	public function init() {
		$this->_config = DispletReader_Woopa_Registry::get('config');
		$this->_db_option_name = $this->_config->db_option_name;

		// this is safe, add_option does nothing if the setting exists
		add_option($this->_db_option_name, $this->_defaults);

		$this->_settings = get_option($this->_db_option_name);

		$this->_init();
	}

	/**
	 * Post-init template method
	 */
	protected function _init() {}

	/**
	 * Save to the database.
	 */
	protected function _save() {
		update_option($this->_db_option_name, $this->_settings);
	}

	/**
	 * Load from the database.
	 */
	protected function _refresh() {
		$this->_settings = get_option($this->_db_option_name);
	}

	/**
	 * Get all the form options
	 */
	public function get_options() {
		return $this->_settings['options'];
	}

	/**
	 * Dump the current settings
	 *
	 * @return The settings.
	 */
	public function dump() {
		return $this->_settings;
	}

	/**
	 * Import settings
	 *
	 * Merges new settings with old with array_merge()
	 */
	public function import_settings($new_settings) {
		$merged = array_merge($this->_settings, $new_settings);
		$this->_settings = $merged;

		update_option($this->_db_option_name, $this->_settings);
	}
}
