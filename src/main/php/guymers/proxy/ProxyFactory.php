<?php

namespace guymers\proxy;

use \ReflectionClass;

use guymers\proxy\internal\ProxyFactory as ProxyFactoryInternal;

class ProxyFactory {

	private static $INITIALISED = false;

	private static function initialise() {
		$cacheDir = Config::$CACHE_DIRECTORY;
		$fileExtension = Config::$FILE_EXTENSION;

		spl_autoload_register(function($class) use ($cacheDir, $fileExtension) {
			$classPath = str_replace("\\", DIRECTORY_SEPARATOR, $class);
			$fileName = $cacheDir . DIRECTORY_SEPARATOR . $classPath . "." . $fileExtension;

			if (file_exists($fileName)) {
				include($fileName);
			}
		});
	}

	/**
	 * Create a proxy for a class with the method hooks
	 *
	 * @param ReflectionClass $class
	 * @param array $methodHooks
	 */
	public static function create(ReflectionClass $class, array $methodHooks) {
		if (!self::$INITIALISED) {
			self::initialise();
			self::$INITIALISED = true;
		}

		$proxyFactory = new ProxyFactoryInternal($class, $methodHooks);

		return $proxyFactory->create();
	}

}
