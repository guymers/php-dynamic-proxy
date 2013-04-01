<?php

namespace guymers\proxy;

use \ReflectionClass;

/**
 * Interface dynamic proxies will implement
 */
interface Proxy {

	/**
	 * @param MethodHook[] $methodHooks
	 */
	public function _setMethodHooks(array $methodHooks);

}
