<?php

namespace guymers\proxy;

use \PHPUnit_Framework_TestCase;
use \ReflectionClass;
use \ReflectionMethod;

use guymers\proxy\mock\Test;

class ProxyTest extends PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function proxy() {
		Config::set(["CACHE_DIRECTORY" => "/tmp"]);

		$class = new ReflectionClass('guymers\proxy\mock\Test');
		$methodHooks = [
			new TestingParams(),
			new TestingTypeHintParams()
		];

		$proxy = ProxyFactory::create($class, $methodHooks);

		$this->assertTrue($proxy instanceof Test);
		$this->assertEquals("", $proxy->getVariable());
		$this->assertEquals("blah", $proxy->testing());

		$this->assertEquals("one - two = ", $proxy->testingParams("one", "two"));
		$this->assertEquals("TestingParams", $proxy->getVariable());

		$this->assertEquals("testingTypeHintParams 1 - 2 = ", $proxy->testingTypeHintParams(new Test()));
		$this->assertEquals("TestingTypeHintParams", $proxy->getVariable());
	}

}

class TestingParams implements MethodHook {

	public function supports(ReflectionMethod $method) {
		return $method->getName() == "testingParams";
	}

	public function invoke($proxy, callable $method, array $args) {
		$proxy->setVariable("TestingParams");

		return $method();
	}

}

class TestingTypeHintParams implements MethodHook {

	public function supports(ReflectionMethod $method) {
		return $method->getName() == "testingTypeHintParams";
	}

	public function invoke($proxy, callable $method, array $args) {
		$proxy->setVariable("TestingTypeHintParams");

		return $method();
	}

}
