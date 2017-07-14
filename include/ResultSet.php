<?php
class DispletReader_ResultSet
{
	protected $_model;

	protected $_displet_request;
	protected $_displet_search;
	protected $_displet_response;

	protected $_criteria;

	protected $_results;

	// lazy loaded meta
	protected $_count = false;
	protected $_ranges = false;

	/**
	 * @param array $criteria Displet Search criteria in key=>val form. 
	 */
	public function __construct(array $criteria) {
		$this->_criteria = $criteria;
		$this->_config = DispletReader_Woopa_Registry::get('config');
		$this->_model = DispletReader_Woopa_Registry::get('model');

		require_once $this->_config->include_dir
			. DIRECTORY_SEPARATOR
			. 'php-displet'
			. DIRECTORY_SEPARATOR
			. 'php-displet.php';

		$this->_init();
	}

	protected function _init() {}

	/**
	 * Initialize
	 */
	public function init() {
		$this->_displet_search = new DispletReader_Displet_Search($this->_criteria, true);
		$this->_displet_request = new DispletReader_Displet_Request($this->_displet_search,
			array(
				'url' => $this->_model->get_url()
			));

		$cached = DispletReader_Gzip_Transients_Cache::get($this->_displet_request->get_generated_url());

		if (DISPLETREADER_DEBUG) {
			error_log('DispletReader: ResultSet init, url: ' . $this->_displet_request->get_generated_url());
		}

		// @todo html comments debug here?

		if ($this->_model->get_comments_debug_enabled()) {
			echo PHP_EOL
				. '<!--' . $this->_displet_request->get_generated_url() . '-->'
				. PHP_EOL;
		}

		// the dance with SimpleXMLElement::asXML() and the SimpleXMLElement contructor
		// is necessary because SimpleXMLElements contain a resource and can't be
		// serialized.
		if ($cached === false) {
			try {
			$this->_displet_response = $this->_displet_request->send();
			//DispletReader_Transients_Cache::set($this->_displet_request->get_generated_url(),
			DispletReader_Gzip_Transients_Cache::set($this->_displet_request->get_generated_url(),
				$this->_displet_response->get_xml()->asXML(),
				$this->_model->get_cache_lifetime());
			$this->_results = $this->_displet_response->get_xml();
			} catch (Exception $e) {}
		}
		else {
			try {
				$this->_results = new SimpleXMLElement($cached);
			}
			catch (Exception $e) {
				$this->_displet_response = $this->_displet_request->send();

				if (DISPLETREADER_DEBUG) {
					error_log('DispletReader: Cached data corrupted, loading live.');
				}

				try {
					$this->_displet_response = $this->_displet_request->send();
					$this->_results = $this->_displet_response->get_xml();
				}
				catch (Exception $e) {
					if (DISPLETREADER_DEBUG) {
						error_log('DispletReader: Couldn\'t parse xml, giving up. Server ' . $this->_displet_request->get_generated_url());
					}
					throw new Exception('Couldn\'t parse xml from ' . $this->_displet_request->get_generated_url() );
				}
			}
		}
	}

	/**
	 * Optionally set a different Displet server before init.
	 */
	public function set_displet_url($url) {
		
	}

	/**
	 * Get the configured Displet server url.
	 *
	 * @return string $url
	 */
	public function get_displet_url() {
		
	}

	/**
	 * Get the url used in the query to the Displet server.
	 *
	 * @return string $url
	 */
	public function get_generated_url() {
		return $this->_displet_request->get_generated_url();
	}

	/**
	 * Count the results
	 *
	 * @return int
	 */
	public function count() {
		return $this->_count();
	}

	/**
	 * Return all results after in SimpleXML.
	 *
	 * @return SimpleXMLElement
	 */
	public function fetch_all() {
		return $this->_results;
	}
	
	/**
	 * Return all results after in SimpleXML.
	 *
	 * @return SimpleXMLElement
	 */
	public function count_active() {
		$results = $this->_results;
		$all_actives=0;
		foreach($results as $result){
			$status = $result->{'status'};
			if (stripos($status,'und')!==false || stripos($status,'pen')!==false || stripos($status,'con')!==false){
			}
			else {
				$all_actives++;
			}
		}
		return $all_actives;
	}
	
	public function count_land_and_ranch() {
		$results = $this->_results;
		$land_and_ranch_listings=0;
		foreach($results as $result){
			if ($result->{'property-type'} == 'Land' || $result->{'property-type'} == 'Ranch'){
				$land_and_ranch_listings++;
			}
		}
		return $land_and_ranch_listings;
	}
	
	public function get_property_types() {
		$results = $this->_results;
		$property_types = array(
			'houses' => false,
			'condos' => false,
			'townhomes' => false,
			'land' => false,
			'more_than_one' => false
		);
		foreach($results as $result){
			if (strpos(strtolower($result->{'property-type'}), 'house') !== false){
				$property_types['houses'] = true;
			}
			if (strpos(strtolower($result->{'property-type'}), 'condo') !== false){
				$property_types['condos'] = true;
			}
			if (strpos(strtolower($result->{'property-type'}), 'townhome') !== false || strpos(strtolower($result->{'property-type'}), 'townhouse') !== false){
				$property_types['townhomes'] = true;
			}
			if (strpos(strtolower($result->{'property-type'}), 'land') !== false){
				$property_types['land'] = true;
			}
		}
		$i = 0;
		foreach ($property_types as $property_type) {
			if ($property_type) {
				$i++;
			}
		}
		if ($i > 1) {
			$property_types['more_than_one'] = true;
		}
		return $property_types;
	}

	public function get_nav_elements() {
		$results_for_nav = $this->_return_sort('list-price', 'ASC');
		$price_range = $this->_get_range('list-price', true);
		if ($this->_count > 24) {
			$number_of_navs = 8;
		}
		else {
			$number_of_navs = 5;
		}
		$listings_per_page = ceil($this->_count / $number_of_navs);
		$nav_positions = array();
		$i = 1;
		$j = 1;
		$last_position = false;
		foreach ($results_for_nav as $result_for_nav) {
			if ($i == 1 || $j == $listings_per_page || $i == $this->_count) {
				$price = $result_for_nav->{'list-price'};
				if ($price > 1000000) {
					$price_round = 250000;
				}
				else if ($price > 500000) {
					$price_round = 100000;
				}
				else {
					$price_round = 50000;
				}
				if ($i == 1) {
					$position = ceil($price / $price_round) * $price_round;
					$nav_positions[] = $position;
					$last_position = $position;
				}
				else if ($i == $this->_count){
					$position = floor($price / $price_round) * $price_round;
					if ($position != $last_position) {
						$nav_positions[] = $position;
						$last_position = $position;
					}
				}
				else{
					$position = round($price / $price_round) * $price_round;
					if ($position != $last_position) {
						$nav_positions[] = $position;
						$last_position = $position;
					}
				}
				$j = 0;
			}
			$i++;
			$j++;
		}
		$nav_positions[] = 999999999;
		$nav_array = array();
		$i = 0;
		$navs_count = count($nav_positions);
		foreach ($nav_positions as $nav_location) {
			if ($i == 0) {
				$nav_min = 0;
			}
			else {
				$nav_min = $previous_nav_location;
			}
			$nav_max = $nav_location;
			if ($nav_min < $nav_max) {
				$nav_array[$i]['min'] = $nav_min;
				$nav_array[$i]['max'] = $nav_max;
			}
			$i++;
			$previous_nav_location = $nav_location;
		}
		return $nav_array;
	}

	/**
	 * Get min and max for specified attribute.
	 *
	 * @param string $attribute_name Attribute of Displet result
	 * @return array
	 */
	public function get_range($attribute_name, $discard_zero = true) {
		return $this->_get_range((string) $attribute_name, (bool) $discard_zero);
	}

	/**
	 * Get mean for specified attribute.
	 *
	 * @param string $attribute_name Attribute of Displet result
	 * @return mixed
	 */
	public function get_mean($attribute_name, $discard_zero = true) {
		return $this->_get_mean($attribute_name, $discard_zero);
	}

	/**
	 * Get median for specified attribute.
	 *
	 * @param string $attribute_name Attribute of Displet result
	 * @return mixed
	 */
	public function get_median($attribute_name, $discard_zero = true) {
		return $this->_get_median($attribute_name, $discard_zero);
	}

	/**
	 * Get mode for specified attribute
	 *
	 * @param string $attribute_name Attribute of a Displet result
	 * @return mixed
	 */
	public function get_mode($attribute_name, $discard_zero = true) {
		return $this->_get_mode($attribute_name, $discard_zero);
	}

	/**
	 * Sort results
	 *
	 * @param string $attribute_name attribute to order by
	 * @param string $direction ASC or DESC (default: ASC)
	 */
	public function sort($attribute_name, $direction = 'ASC') {
		$this->_sort($attribute_name, $direction);
	}

	/**
	 * Fetch a subset of the results.
	 *
	 * @param int $offset
	 * @param int $length
	 */
	public function fetch_subset($offset, $length) {
		return $this->_fetch_subset($offset, $length);
	}

	protected function _fetch_subset($offset, $length) {
		$dom_subset = new DOMDocument;
		$residentials = $dom_subset->appendChild($dom_subset->createElement('residentials'));
		$residentials->setAttribute('type', 'array');

		$dom_results = dom_import_simplexml($this->_results)->childNodes;
		$dom_results_count = $dom_results->length;

		// if the offset is valid
		if (($dom_results_count - 1) > $offset) {
			// if the length is valid
			if (($offset + ($length - 1)) < $dom_results_count) {
				$last_i = $offset + ($length - 1);
			}
			else {
				$last_i = $dom_results_count - 1;
			}

			for ($i = $offset; $i <= $last_i; $i++ ) {
				$copy = $dom_subset->importNode($dom_results->item($i), true);
				$residentials->appendChild($copy);
			}
		}

		return simplexml_import_dom($residentials);
	}

	protected function _sort($attribute_name, $direction) {
		$this->_current_sort_attribute = $attribute_name;
		$this->_current_sort_direction = $direction;

		$tmp_array = array();
		if (!empty($this->_results)) {
			foreach ($this->_results as $residential) {
				$tmp_array[] = $residential;
			}
			$dom_element = dom_import_simplexml($this->_results);
		}
		if (!empty($dom_element)) {
			while ($dom_element->hasChildNodes()) {
				$dom_element->removeChild($dom_element->firstChild);
			}
			usort($tmp_array, array(&$this, '_sort_callback'));
			foreach ($tmp_array as $simplexml) {
				$residential = dom_import_simplexml($simplexml);
				$dom_element->appendChild($residential);
			}
			$this->_results = simplexml_import_dom($dom_element);
		}
	}

	protected function _return_sort($attribute_name, $direction) {
		$this->_current_sort_attribute = $attribute_name;
		$this->_current_sort_direction = $direction;

		$tmp_array = array();
		foreach ($this->_results as $residential) {
			$tmp_array[] = $residential;
		}

		$dom_element = dom_import_simplexml($this->_results);

		while ($dom_element->hasChildNodes()) {
			$dom_element->removeChild($dom_element->firstChild);
		}

		usort($tmp_array, array(&$this, '_sort_callback'));

		foreach ($tmp_array as $simplexml) {
			$residential = dom_import_simplexml($simplexml);

			$dom_element->appendChild($residential);
		}
		$return_residentials = simplexml_import_dom($dom_element);
		return $return_residentials;
	}

	protected function _sort_callback($a, $b) {
		$type = $a->attributes()->type;

		switch ($type) {
		case 'integer':
			$a = (int) $a->{$this->_current_sort_attribute};
			$b = (int) $b->{$this->_current_sort_attribute};
			break;
		case 'datetime':
			$a = (int) str_replace(array('-','T',':','Z'), '', $a->{$this->_current_sort_attribute});
			$b = (int) str_replace(array('-','T',':','Z'), '', $b->{$this->_current_sort_attribute});
			break;
		default:
			$a = (float) $a->{$this->_current_sort_attribute};
			$b = (float) $b->{$this->_current_sort_attribute};
		}

		if ($this->_current_sort_direction === 'DESC') {
			if ($a < $b) {
				return 1;
			}
			else if ($a == $b) {
				return 0;
			}
			else {
				return -1;
			}
		}
		else {
			if ($a > $b) {
				return 1;
			}
			else if ($a == $b) {
				return 0;
			}
			else {
				return -1;
			}
		}
	}

	protected function _get_mode($attribute_name, $discard_zero) {
		if (!isset($this->_modes[$attribute_name])) {
			$mode = false;
			$values = $this->_make_values_for_stats($attribute_name, $discard_zero);
			$counts = array_count_values($values);
	
			if (current($counts) > 1) {
				$mode = key($counts);
			}

			$this->_modes[$attribute_name] = $mode;
		}

		return $this->_modes[$attribute_name];
	}

	protected function _get_median($attribute_name, $discard_zero) {
		if (!isset($this->_medians[$attribute_name])) {
			$values = $this->_make_values_for_stats($attribute_name, $discard_zero);
			sort($values);
			$count = count($values);

			if ($count > 1) {
				if ($count % 2 !== 0) {
					$i = ($count + 1) / 2;
					$median = $values[$i];
				}
				else if ($count === 2){
					$median = ($values[0] + $values[1]) / 2;
				}
				else {
					$i1 = floor($count / 2);
					$i2 = $i1++;
					$median = ($values[$i1] + $values[$i2]) / 2;
				}
			}
			else {
				$median = $values[0];
			}

			$this->_medians[$attribute_name] = $median;
		}

		return $this->_medians[$attribute_name];
	}

	protected function _get_mean($attribute_name, $discard_zero) {
		if (!isset($this->_means[$attribute_name])) {
			$values = $this->_make_values_for_stats($attribute_name, $discard_zero);

			$this->_means[$attribute_name] = array_sum($values) / count($values);
		}

		return $this->_means[$attribute_name];
	}

	protected function _make_values_for_stats($attribute_name, $discard_zero) {
		$values = array();

		foreach ($this->_results as $result) {
			if (isset($result->$attribute_name)) {
				if (isset($result->$attribute_name->attributes()->type)) {
					$type = $result->$attribute_name->attributes()->type;

					switch($type) {
					case 'integer':
						$value = (int) $result->$attribute_name;

						if ($discard_zero === false) {
							$values[] = $value;
						}
						else if ($value !== 0) {
							$values[] = $value;
						}

						break;
					}
				}
			}
		}

		if (count($values) === 0) {
			throw new Exception('No values for attribute.');
		}

		return $values;
	}

	protected function _count() {
		if (!$this->_count) {
			$this->_count = count($this->_results);
		}

		return $this->_count;		
	}

	protected function _get_range($attribute_name, $discard_zero) {
		if (!isset($this->_ranges[$attribute_name])) {
			$values = $this->_make_values_for_stats($attribute_name, $discard_zero);
			$min = min($values);
			$max = max($values);

			$this->_ranges[$attribute_name] = array(
				'min' => $min,
				'max' => $max,
				'range' => ($max - $min)
			);
		}

		return $this->_ranges[$attribute_name];
	}
}
