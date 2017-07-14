<?php
/**
 * DispletReader_Woopa Registry static class.
 *
 * @ingroup DispletReader_Woopa
 */
class DispletReader_Woopa_Registry
{
	protected static $_items = array();

	/**
	 * Get an item in the registry.
	 *
	 * @param $key Item key.
	 *
	 * @return Item.
	 */
	public static function get($key) {
		if (array_key_exists($key, self::$_items)) {
			return self::$_items[$key];
		}

		throw new Exception('Setting not found');
	}

	/**
	 * Set an item in the registry.
	 *
	 * @param $key Item key.
	 * @param $value Item value.
	 */
	public static function set($key, $value) {
		self::$_items[$key] = $value;
	}

	/**
	 * Dump all contents of the registry.
	 *
	 * @return contents
	 */
	public static function dump() {
		return self::$_items;
	}
}
