<?php

namespace guymers\proxy;

use \ReflectionMethod;

/**
 * Defines a hook for a method
 */
interface MethodHook {

	/**
	 * Does this hook support this method
	 *
	 * @param ReflectionMethod $method
	 * @return boolean
	 */
	public function supports(ReflectionMethod $method);

	/**
	 * Called instead of the original method
	 *
	 * @param mixed $proxy the proxy object
	 * @param ReflectionMethod $method original method
	 * @param array $args original methods arguments
	 */
	public function invoke($proxy, ReflectionMethod $method, array $args);

}
