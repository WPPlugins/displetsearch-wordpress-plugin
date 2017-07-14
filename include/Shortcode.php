<?php
abstract class DispletReader_Shortcode
{
	protected $_attributes = array();
	protected $_name = false;
	protected $_config = false;
	protected $_model = false;

	public function __construct($name = false) {
		if ($name) {
			$this->_name = $name;
		}
		else if (!$this->_name) {
			throw new Exception('Need a name.');
		}

		$this->_config = DispletReader_Woopa_Registry::get('config');
		$this->_model = DispletReader_Woopa_Registry::get('model');
		$this->_view = new DispletReader_View;
		$this->_register();
		$this->_enqueue();
	}

	public function callback(array $attributes) {
		$this->_attributes = $attributes;
		$this->_init();
		return $this->_render();
	}

	protected function _init() {}

	protected function _enqueue() {}

	protected function _render() {
		ob_start();
		echo '<pre>';
		echo 'No render method defined.' . PHP_EOL;
		echo 'Attributes:' . PHP_EOL;
		print_r($this->_attributes);
		echo '</pre>';
		$message = ob_get_contents();
		ob_end_clean();
		return $message;
	}

	protected function _register() {
		add_shortcode($this->_name, array(&$this, 'callback'));
	}
}
