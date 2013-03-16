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
	 * @param callable $method original method wrapped in a closure
	 * @param array $args original methods arguments
	 */
	public function invoke($proxy, callable $method, array $args);

}
