<?php
/**
 * Global configuration object
 *
 * @ingroup DispletReader_Woopa
 */
abstract class DispletReader_Woopa_Config
{
	protected $_config = Array();

	public function __construct(array $config_defaults) {
		$this->dir = $config_defaults['dir'];

		$this->url = $config_defaults['url'];

		$this->template_dir = $this->dir
			. DIRECTORY_SEPARATOR
			. DIRECTORY_SEPARATOR
			. 'templates';

		$this->admin_template_dir = $this->dir
			. DIRECTORY_SEPARATOR
			. 'templates'
			. DIRECTORY_SEPARATOR
			. 'admin';

		$this->include_dir = $this->dir
			. DIRECTORY_SEPARATOR
			. 'include';

		$this->admin_page_slug = $config_defaults['name'];

		$this->settings_name = $config_defaults['name'];

		foreach ($config_defaults as $k => $v) {
			$this->$k = $v;
		}
	}

	public function __get($key) {
		if (array_key_exists($key, $this->_config)) {
			return $this->_config[$key];
		}

		throw new Exception('Setting not found');
	}

	public function __set($key, $value) {
		$this->_config[$key] = $value;
	}
}
