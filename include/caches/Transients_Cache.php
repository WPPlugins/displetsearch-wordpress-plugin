<?php
class DispletReader_Transients_Cache implements DispletReader_Cache
{
	const db_prefix = 'DReader_';

	public static function get($url) {
		$key = self::db_prefix . md5($url);
		$value = get_transient($key);

		if ($value !== false) {
			if (DISPLETREADER_DEBUG) {
				error_log('DispletReader Transients Cache: Cache hit, hash ' . $key);
			}
			return $value;
		}
		else {
			if (DISPLETREADER_DEBUG) {
				error_log('DispletReader Transients Cache: Cache miss, hash ' . $key);
			}
			return false;
		}
	}

	public static function set($url, $value, $expires) {
		$key = self::db_prefix . md5($url);
		set_transient($key, $value, $expires);

		if (DISPLETREADER_DEBUG) {
			error_log('DispletReader Transients Cache: Saved results from ' . $url 
				. ', key ' . $key . ', expires ' . $expires);
		}
	}
}
