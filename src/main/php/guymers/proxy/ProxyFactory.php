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
	 * Create a proxy for a class with the provided method hooks.
	 *
	 * If an argument array is provided the proxy's constructor is called with
	 * it, otherwise the constructor is not called.
	 *
	 * @param ReflectionClass $class
	 * @param MethodHook[] $methodHooks
	 * @param array $args
	 * @return mixed a proxy object for class
	 */
	public static function create(ReflectionClass $class, array $methodHooks, array $args = null) {
		if (!self::$INITIALISED) {
			self::initialise();
			self::$INITIALISED = true;
		}

		$proxyFactory = new ProxyFactoryInternal($class, $methodHooks);

		return $proxyFactory->create($args);
	}

}
