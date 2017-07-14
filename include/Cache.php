<?php
interface DispletReader_Cache
{
	public static function get($key);

	public static function set($key, $value, $expires);
}
