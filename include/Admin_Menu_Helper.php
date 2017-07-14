<?php
class DispletReader_Admin_Menu_Helper
{
	protected $_model;
	protected $_config;
	protected $_settings;

	public function __construct() {
		$this->_model = DispletReader_Woopa_Registry::get('model');
		$this->_config = DispletReader_Woopa_Registry::get('config');
		$this->_settings = $this->_model->get_settings();
		$this->_option_name = $this->_config->db_option_name;
	}

	public function enabled() {
		if ($this->_settings['enabled']) {
?>
<input type="radio"
	name="<?php echo $this->_option_name ?>[enabled]"
	id="displetreader-enabled"
	value="1"
	checked="checked"/>Enabled<br/>
<input type="radio"
	name="<?php echo $this->_option_name ?>[enabled]"
	id="displetreader-enabled"
	value="0"/>Disabled (shortcodes hidden)
<?php
		}
		else {
?>
<input type="radio"
	name="<?php echo $this->_option_name ?>[enabled]"
	id="displetreader-enabled"
	value="1"/>Enabled<br/>
<input type="radio"
	name="<?php echo $this->_option_name ?>[enabled]"
	id="displetreader-enabled"
	value="0"
	checked="checked"/>Disabled (shortcodes hidden)
<?php
		}
	}

	public function displet_url() {
?>
<input type="text"
	name="<?php echo $this->_option_name ?>[displet_url]"
	id="displetreader-displet_url"
	value="<?php echo esc_attr($this->_settings['displet_url']) ?>"/>
<?php
	}

	public function rows_per_page() {
?>
<input type="text"
	size="3"
	name="<?php echo $this->_option_name ?>[rows_per_page]"
	id="displetreader-rows_per_page"
	value="<?php echo (int) $this->_settings['rows_per_page'] ?>"/>
<?php
	}

	public function cache_lifetime() {
?>
<input type="text"
	size="6"
	name="<?php echo $this->_option_name ?>[cache_lifetime]"
	id="displetreader-cache_lifetime"
	value="<?php echo (int) $this->_settings['cache_lifetime'] ?>"/>
<?php
	}
}
