<?php
abstract class DispletReader_TinyMCE_Plugin
{
	protected $_wp_tinymce_rows = array(
		'1' => 'mce_buttons',
		'2' => 'mce_buttons_2',
		'3' => 'mce_buttons_3',
		'4' => 'mce_buttons_4'
	);

	protected $_row = false;
	protected $_url = false;
	protected $_name = false;
	protected $_left_separator = false;
	protected $_right_separator = false;

	public function __construct($url = false,
		$name = false,
		$row = 1,
		$left_separator = false,
		$right_separator = false) {

		if (!$url && !$this->_url) {
			throw new Exception('Needs a url.');
		}

		if (!$name && !$this->_name) {
			throw new Exception('Needs a name.');
		}

		if ($url) {
			$this->_url = $url;
		}

		if (!$this->_row) {
			$this->_row = (int) $row;
		}
	}

	public function register() {
		add_filter('mce_external_plugins', array(&$this, 'external_plugins_callback'));
		add_filter($this->_wp_tinymce_rows[$this->_row], array(&$this, 'buttons_callback'));
	}

	public function external_plugins_callback(array $plugins) {
		$plugins[$this->_name] = $this->_url;
		return $plugins;
	}

	public function buttons_callback($buttons) {
		if ($this->_left_separator) {
			array_push($buttons, '|', $this->_name);
		}
		else {
			array_push($buttons, $this->_name);
		}

		if ($this->_right_separator) {
			array_push($buttons, '|');
		}
		return $buttons;
	}
}
