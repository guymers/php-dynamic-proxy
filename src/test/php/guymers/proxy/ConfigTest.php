<?php

namespace guymers\proxy;

use \PHPUnit_Framework_TestCase;

class ConfigTest extends PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function config() {
		$cacheDirectory = "/test";
		$config = [
			"CACHE_DIRECTORY" => $cacheDirectory
		];
		Config::set($config);

		$this->assertEquals($cacheDirectory, Config::$CACHE_DIRECTORY);
	}

}
