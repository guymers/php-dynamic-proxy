<?php

namespace guymers\proxy;

use \PHPUnit_Framework_TestCase;
use \ReflectionClass;
use \ReflectionMethod;
use \FilesystemIterator;
use \RecursiveDirectoryIterator;
use \RecursiveIteratorIterator;

use guymers\proxy\mock\Test;
use guymers\proxy\mock\TestNullable;

class ProxyTest extends PHPUnit_Framework_TestCase {

	private $class;
	private $proxy;

	public function setUp() {
		$cacheDirectory = "/tmp/php-dynamic-proxy";

		$this->deleteDirectory($cacheDirectory);

		Config::set(["CACHE_DIRECTORY" => $cacheDirectory]);

		$this->class = new ReflectionClass('guymers\proxy\mock\Test');
		$methodHooks = [
			new TestingParams(),
			new TestingTypeHintParams()
		];

		$this->proxy = ProxyFactory::create($this->class, $methodHooks);
	}

	/**
	 * @param string $directory
	 * @see http://stackoverflow.com/questions/12966227/#answer-12966487
	 */
	private function deleteDirectory($directory) {
		if (!is_dir($directory)) {
			return;
		}

		$directoryIterator = new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS);
		$iterator = new RecursiveIteratorIterator($directoryIterator, RecursiveIteratorIterator::CHILD_FIRST);

		foreach ($iterator as $file) {
			if ($file->isDir()) {
				rmdir($file);
			} else {
				unlink($file);
			}
		}
	}

	/**
	 * @test
	 */
	public function proxy() {
		$this->assertTrue($this->proxy instanceof Test);
		$this->assertEquals("", $this->proxy->getVariable());
		$this->assertEquals("blah", $this->proxy->testing());

		$this->assertEquals("one - two = ", $this->proxy->testingParams("one", "two"));
		$this->assertEquals("TestingParams", $this->proxy->getVariable());

		$test = new Test("");
		$this->assertEquals("testingTypeHintParams 1 - 2 = ", $this->proxy->testingTypeHintParams($test));
		$this->assertEquals("TestingTypeHintParams", $this->proxy->getVariable());
	}

	/**
	 * @test
	 */
	public function proxyArgs() {
		$arg = "blah";
		$args = [$arg];
		$proxy = ProxyFactory::create($this->class, [], $args);

		$this->assertTrue($proxy instanceof Test);
		$this->assertEquals($arg, $proxy->getConstructorVariable());
	}

	/**
	 * @test
	 * @expectedException guymers\proxy\exception\AlreadyProxyException
	 */
	public function alreadyProxy() {
		$class = new ReflectionClass($this->proxy);

		ProxyFactory::create($class, []);
	}

	/**
	 * @test
	 * @expectedException guymers\proxy\exception\HookAlreadyDefinedException
	 */
	public function moreThanOneHookForAMethod() {
		$methodHooks = [
			new TestingParams(),
			new TestingParams()
		];

		ProxyFactory::create($this->class, $methodHooks);
	}

	/**
	 * @test
	 * @requires PHP 7.1
	 */
	public function proxyNullable() {
		$proxy = ProxyFactory::create(new ReflectionClass('guymers\proxy\mock\TestNullable'), []);
		$this->assertTrue($proxy instanceof TestNullable);
	}
}

class TestingParams implements MethodHook {

	public function supports(ReflectionMethod $method) {
		return $method->getName() == "testingParams";
	}

	public function invoke($proxy, ReflectionMethod $method, array $args) {
		$proxy->setVariable("TestingParams");

		return $method->invokeArgs($proxy, $args);
	}

}

class TestingTypeHintParams implements MethodHook {

	public function supports(ReflectionMethod $method) {
		return $method->getName() == "testingTypeHintParams";
	}

	public function invoke($proxy, ReflectionMethod $method, array $args) {
		$proxy->setVariable("TestingTypeHintParams");

		return $method->invokeArgs($proxy, $args);
	}

}
