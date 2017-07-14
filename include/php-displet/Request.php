<?php
/**
 * Request object
 *
 * @ingroup php_displet
 */
class DispletReader_Displet_Request
{
	protected $_search;
	protected $_base_url;
	protected $_query_string;
	protected $_generated_url;
	protected $_type;
	protected $_response;

	public function __construct(DispletReader_Displet_Search $search, array $config = array()) {
		$this->_search = $search;

		if (!empty($config)) {
			$this->_base_url = $config['url'];
			$type = new stdClass();
			$type->_type = (empty($config['url'])) ? 'map' : $config['url'];
			$this->_generate_url();
		}
	}

	/**
	 * Set the desired response type for DispletReader_Displet
	 *
	 * @param $type "map" or "list"
	 */
	public function set_type($type) {
		if ($type === 'map' || $type === 'list') {
			$this->_type = $type;
		}
	}

	/**
	 * Get the response type
	 *
	 * @return string type "map" or "list"
	 */
	public function get_type() {
		return $this->_type;
	}

	/**
	 * Set the url of the DispletReader_Displet server
	 */
	public function set_base_url($url) {
		// @todo make sure we have a scheme, valid address or domain, and a trailing slash
		$this->_base_url = $url;
	}

	/*
	 * Get the base url of the DispletReader_Displet server
	 * @return string Base url.
	 */
	public function get_base_url() {
		return $this->_base_url;
	}

	/**
	 * Get the url generated based on request type and parameters
	 *
	 * @return string
	 */
	public function get_generated_url() {
		return $this->_generated_url;
	}

	/**
	 * Send the request to the DispletReader_Displet server
	 *
	 * @return DispletReader_Displet_Response
	 */
	public function send() {		
		return $this->_send();
	}

	protected function _send() {
		$curl_result = $this->_curl_data();
		
		$response = new DispletReader_Displet_Response(array(
			'code' => $curl_result['info']['http_code'],
			'data' => $curl_result['result']));

		return $response;
	}

	protected function _generate_url() {
		if (!isset($this->_type)) {
			$this->_type = 'list';
		}

		switch ($this->_type) {
		case 'list':
			$criteria = $this->_search->to_array();

			// DispletReader_Displet API expects csvs for arrays
			foreach ($criteria as $k => &$v) {
				if (is_array($v)) {
					$criteria[$k] = implode(',', $v);
				}
			}

			//$this->_query_string = http_build_query($criteria);
			$this->_query_string = preg_replace('/status=/','status[]=', http_build_query($criteria));

			$url = $this->_base_url
				. 'residentials/results.xml'
				. '?' . $this->_query_string;
			break;
		}

		// @todo sanitize url

		$this->_generated_url = $url;
	}

	protected function _curl_data() {
		$handle = curl_init($this->_generated_url);

		curl_setopt_array($handle, array(
			CURLOPT_HEADER => false,
			CURLOPT_RETURNTRANSFER => true
		));

		$result = curl_exec($handle);

		$info = curl_getinfo($handle);

		curl_close($handle);

		return array(
			'result' => $result,
			'info' => $info
		);
	}
}
