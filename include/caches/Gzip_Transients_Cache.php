<?php
class DispletReader_Gzip_Transients_Cache implements DispletReader_Cache
{
	const db_prefix = 'DReader_';
	const transient_prefix = '_transient_';
	const inflate_error = 1;
	const deflate_error = 2;

	public static function get($url) {
		$key = self::db_prefix . md5($url);
		$value = get_transient($key);
		$inflated = self::_inflate($value);

		if ($inflated === self::inflate_error) {
			// Maybe it's not compressed?
			error_log('DispletReader: Gzip Transients Cache: Inflate error, trying Transients Cache.');
			return DispletReader_Transients_Cache::get($value);
		}

		if ($value !== false) {
			if (DISPLETREADER_DEBUG) {
				error_log('DispletReader: Gzip Transients Cache: Cache hit, hash ' . $key);
			}
			return $inflated;
		}
		else {
			if (DISPLETREADER_DEBUG) {
				error_log('DispletReader: Gzip Transients Cache: Cache miss, hash ' . $key);
			}
			return false;
		}
	}

	public static function set($url, $value, $expires) {
		$key = self::db_prefix . md5($url);
		$value = self::_deflate($value);

		set_transient($key, $value, $expires);

		if (DISPLETREADER_DEBUG) {
			error_log('DispletReader: Gzip Transients Cache: Saved results from ' . $url 
				. ', key ' . $key . ', expires ' . $expires);
		}		
	}

	public static function clear() {
		if (!defined('DISPLETREADER_DEBUG')) {
			// we probably got here from the uninstall script
			define('DISPLETREADER_DEBUG', true);
		}

		if (DISPLETREADER_DEBUG) {
			error_log('DispletReader: Gzip Transients Cache: Clear requested.');
		}

		$transient_name = '_transient_' . self::db_prefix;
		$transient_name = str_replace('_', '\\_', $transient_name);

		global $wpdb;
		$query = 'SELECT option_name FROM ' . $wpdb->options
			. " WHERE option_name LIKE '" . $transient_name . "%'";
		$r = $wpdb->get_results($query, ARRAY_A);

		foreach ($r as $v) {
			$name = str_replace(self::transient_prefix, '', $v['option_name']);

			$deleted = delete_transient($name);

			if (DISPLETREADER_DEBUG) {
				if ($deleted) {
					error_log('DispletReader: Gzip Transients Cache: '
						. $name . ' deleted.');
				}
				else {
					error_log('DispletReader: Gzip Transients Cache: '
						. $name . "Couldn't delete " . $name);
				}
			}
		}
	}

	// gzip then base64 encode
	private static function _deflate($string) {
		if (!$string) {
			return false;
		}

		if (DISPLETREADER_DEBUG) {
			$original_size = strlen($string);
			error_log('DispletReader: Gzip Transients Cache: Deflating. Original size: ' . $original_size);
		}

		$deflated = gzdeflate($string);

		if (DISPLETREADER_DEBUG) {
			$deflated_size = strlen($deflated);
			error_log('DispletReader: Gzip Transients Cache: Deflated size: ' . $deflated_size);
			$base64_size = strlen(base64_encode($deflated));
			error_log('DispletReader: Gzip Transients Cache: Base64 encoded size: '. $base64_size);
			$compression_amount = $base64_size / $original_size * 100 . '% of original size.';
			error_log('DispletReader: Gzip Transients Cache: Compressed, '. $compression_amount . '%');
		}

		if (!$deflated ) {
			return self::deflate_error;
		}
		else {
			return base64_encode($deflated);
		}
	}

	// base64 decode then unzip
	private static function _inflate($string) {
		if (!$string) {
			return false;
		}

		$string = @gzinflate(base64_decode($string));

		if (!$string) {
			return self::inflate_error;
		}
		else {
			return $string;
		}
	}
}
