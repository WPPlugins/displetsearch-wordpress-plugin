<?php
/**
 * Response object
 *
 * @ingroup php_displet
 */
class DispletReader_Displet_Response
{
	protected $_code;
	protected $_data;
	protected $_xml;

	public function __construct($array = array(
		'data' => false,
		'code' => false,
		'xml' => false)) {

		if (isset($array['data'])) {
			$this->_data = $array['data'];
		}

		if (isset($array['code'])) {
			$this->_code = $array['code'];
		}

		if (isset($array['xml'])) {
			$this->_xml = $array['xml'];
		}
	}

	/**
	 * Set the http response code
	 */
	public function set_code($code) {
		$this->_code = (int) $code;
	}

	/**
	 * Get the http response code.
	 *
	 * @return HTTP code
	 */
	public function get_code() {
		return $this->_code;
	}

	/**
	 * Set data returned by DispletReader_Displet server.
	 */
	public function set_data($data) {
		$this->_data = $data;
	}

	/**
	 * Get data returned by DispletReader_Displet server.
	 */
	public function get_data() {
		return $this->_data;
	}

	/**
	 * Get the results as a SimpleXML object
	 *
	 * @return SimpleXMLElement results
	 */
	public function get_xml() {
		if (!isset($this->_xml)) {
			$this->_xml = new SimpleXMLElement($this->_data);
		}

		return $this->_xml;
	}
}
