<?php

namespace guymers\proxy;

use \ReflectionMethod;

class MethodAndHook {

	/**
	 * @var ReflectionMethod
	 */
	private $method;

	/**
	 * @var MethodHook
	 */
	private $hook;

	public function __construct(ReflectionMethod $method, MethodHook $hook) {
		$this->method = $method;
		$this->hook = $hook;
	}

	/**
	 * @return ReflectionMethod
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * @return MethodHook
	 */
	public function getHook() {
		return $this->hook;
	}

}
