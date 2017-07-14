<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Admin_Ajax_Interface.php';

/**
 * Ajax handler
 *
 * Use by passing parameter arrays to process_post() and process_get()
 *
 * @ingroup DispletReader_Woopa
 */
abstract class DispletReader_Woopa_Admin_Ajax implements DispletReader_Woopa_Admin_Ajax_Interface
{
	protected $model;
	protected $config;

	public function __construct() {
		$this->model = DispletReader_Woopa_Registry::get('model');
		$this->config = DispletReader_Woopa_Registry::get('config');
		$this->init();
	}

	public function init() { }

	/**
	 * Process an ajax post request
	 *
	 */
	public function process_post(array $post) { }

	/**
	 * Process an ajax get request
	 */
	public function process_get(array $get) { }

	protected function _export() {
		$settings = $this->model->dump();
		$string = serialize($settings);
		$length = strlen($string);

		header('Content-type: text/plain');
		header('Content-disposition: attachment; filename='
			. $this->config->name . '-settings.cfg');
		echo $string;
	}
}
