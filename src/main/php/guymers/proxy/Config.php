<?php

namespace guymers\proxy;

class Config {

	public static $PROXY_IMPLEMENTATION = "guymers\proxy\Proxy";
	public static $FILE_EXTENSION = "php";
	public static $CACHE_DIRECTORY = "/tmp/php-dynamic-proxy";
	public static $CACHE_DIRECTORY_PERMISSIONS = 0755;
	public static $CACHE_FILE_PERMISSIONS = 0644;

	public static function set(array $config = []) {
		foreach ($config as $key => $value) {
			self::${$key} = $value;
		}
	}

}
