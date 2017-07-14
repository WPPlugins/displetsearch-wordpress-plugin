<?php
/**
 * Search object
 *
 * @ingroup php_displet
 */
class DispletReader_Displet_Search
{
	protected static $_known_fields = array(
		'minListPrice' => array('type' => 'int_nonzero'),
		'maxListPrice' => array('type' => 'int_nonzero'),
		'minBedrooms' => array('type' => 'int_nonzero'),
		'maxBedrooms' => array('type' => 'int_nonzero'),
		'minBathrooms' => array('type' => 'int_nonzero'),
		'maxBathrooms' => array('type' => 'int_nonzero'),
		'minSquareFeet' => array('type' => 'int_nonzero'),
		'maxSquareFeet' => array('type' => 'int_nonzero'),
		'area' => array('type' => 'int_nonzero'),
		'area_mls_defined' => array('type' => 'string'),
		'city' => array('type' => 'string'),
		'property_type' => array('type' => 'string_array'),
		'minStories' => array('type' => 'int_nonzero'),
		'waterfront' => array('type' => 'bool'),
		'pool_on_property' => array('type' => 'bool'),
		'subdivision' => array('type' => 'string'),
		'keyword' => array('type' => 'string'),
		'school' => array('type' => 'string'),
		'school_district' => array('type' => 'string'),
		'minAcres' => array('type' => 'int_nonzero'),
		'zip' => array('type' => 'string_array'),
		'county' => array('type' => 'string_array'),
		'is_foreclosure' => array('type' => 'bool'),
		'yearBuilt' => array('type' => 'int_nonzero'),
		'status' => array('type' => 'string'),
		'short_sale' => array('type' => 'bool'),
		'last_modified' => array('type' => 'int_nonzero'),
		'listing_agent_id' => array('type' => 'string'),
		'listing_office_id' => array('type' => 'string'),
		'min_lot_size' => array('type' => 'float'),
		'is_gated_community' => array('type' => 'bool'),
		'min_sold_date' => array('type' => 'date'),
		'max_sold_date' => array('type' => 'date'),
		'mls_number' => array('type' => 'string_array'),
		'street_name' => array('type' => 'string'),
		'street_number' => array('type' => 'string'),
		'searchType' => array('type' => 'string')
	);

	protected $_fields = array();

	public function __construct(array $fields = array(), $convert_case = false) {
		if ($convert_case) {
			$fields = self::_convert_case($fields);
		}

		if (self::_check_street($fields)) {
			$this->searchType = 'street';
		}

		foreach ($fields as $name => $value) {
			$this->_filter($name, $value);
		}
	}

	public function __get($name) {
		if (isset($this->_fields[$name])) {
			return $this->_fields[$name];
		}
	}

	public function __set($name, $value) {
		$this->_filter($name, $value);
	}

	public function to_array($filter_empty = false) {
		if ($filter_empty === true) {
			$array = array();
			foreach ($this->_fields as $k => $v) {
				if (!empty($v)) {
					$array[$k] = $v;
				}
			}
			return $array;
		}
		else {
			return $this->_fields;
		}
	}

	public static function get_known_fields() {
		$fields = array();

		foreach (self::_known_fields as $k => $v) {
			$fields[] = $k;
		}

		return $fields;
	}

	private function _filter($name, $value) {
		if (isset(self::$_known_fields[$name])) {

			switch (self::$_known_fields[$name]['type']) {
			case 'int':
				$this->_fields[$name] = (int) $value;
				break;

			case 'int_nonzero':
				$v = (int) $value;
				if ($v !== 0) {
					$this->_fields[$name] = $v;
				}
				break;

			case 'string':
				$v = filter_var($value, FILTER_SANITIZE_STRING);
				if (!empty($v)) {
					$this->_fields[$name] = filter_var($value, FILTER_SANITIZE_STRING);
				}
				break;

			case 'string_array':
				$array = array();

				$value = explode(',', $value);
				if (is_array($value)) {
					foreach ($value as $v) {
						if (!empty($v) && !($v == 'any' || $v == 'Any')) {
							$array[] = trim(filter_var($v, FILTER_SANITIZE_STRING));
						}
					}
				}

				if (count($array) > 0) {
					$this->_fields[$name] = $array;
				}

				break;

			case 'bool':
				if (!($value == 'any' || empty($value))) {
					if ($value === 'Y' || $value === 'N') {
						$this->_fields[$name] = $value;
					}

					if ($value === false) {
						$this->_fields[$name] = 'N';
					}
					else if ($value) {
						$this->_fields[$name] = 'Y';
					}
				}
				break;

			case 'float':
				$this->_fields[$name] = (float) $value;
				break;

			case 'date':
				$this->_fields[$name] = filter_var($value, FILTER_SANITIZE_STRING);
			}
		}
	}

	private static function _convert_case($fields) {
		$converted_fields = array();

		foreach (self::$_known_fields as $k => $v) {
			$lc_known_field = strtolower($k);

			foreach ($fields as $key => $val) {
				if (strtolower($key) === $lc_known_field) {
					$converted_fields[$k] = $val;
				}
			}
		}

		return $converted_fields;
	}

	private static function _check_street($fields) {
		if (isset($fields['street_number']) ||
			isset($fields['street_name'])) {
			return true;
		}

		return false;
	}
}
