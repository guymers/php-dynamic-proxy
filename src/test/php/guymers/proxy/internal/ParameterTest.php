<?php

namespace guymers\proxy\internal;

use \PHPUnit_Framework_TestCase;
use \ReflectionClass;

use guymers\proxy\mock\Test;
use guymers\proxy\mock\TestNullable;

class ParameterTest extends PHPUnit_Framework_TestCase {

	private $parameterClassAndVariable;
	private $parameterClassAndDefault;
	private $parameterClassNotLoaded;

	public function setUp() {
		$test = new Test("");
		$class = new ReflectionClass($test);
		$method = $class->getMethod("testingTypeHintParams");
		$parameters = $method->getParameters();

		$parameter = $parameters[0];
		$this->parameterClassAndVariable = new Parameter($parameter);

		$parameter = $parameters[1];
		$this->parameterClassAndDefault = new Parameter($parameter);

		$method = $class->getMethod("testingTypeHintParamsClassNotLoaded");
		$parameter = $method->getParameters()[0];
		$this->parameterClassNotLoaded = new Parameter($parameter);
	}

	/**
	 * @test
	 */
	public function asString() {
		$this->assertEquals('$param1', $this->parameterClassAndVariable->asString());
		$this->assertEquals('$param2', $this->parameterClassAndDefault->asString());
		$this->assertEquals('$param', $this->parameterClassNotLoaded->asString());
	}

	/**
	 * @test
	 */
	public function asFullString() {
		$this->assertEquals('\guymers\proxy\mock\Test $param1', $this->parameterClassAndVariable->asFullString());
		$this->assertEquals('\guymers\proxy\mock\Test $param2 = NULL', $this->parameterClassAndDefault->asFullString());
		$this->assertEquals('\guymers\proxy\blah\TestNotLoaded $param', $this->parameterClassNotLoaded->asFullString());
	}

	/**
	 * @test
	 * @requires PHP 7.1
	 */
	 public function asFullStringWithNullable() {
		$test2 = new TestNullable("");
		$class = new ReflectionClass($test2);
		$method = $class->getMethod("testingNullableTypeHintParams");
		$parameters = $method->getParameters();

		$parameter = $parameters[0];
		$parameterClassAndReference = new Parameter($parameter);

		$parameter = $parameters[1];
		$parameterClassAndDefault = new Parameter($parameter);
		$this->assertEquals('?\guymers\proxy\mock\Test $param1Nullable', $parameterClassAndReference->asFullString());
		$this->assertEquals('\guymers\proxy\mock\Test $param2Nullable = NULL', $parameterClassAndDefault->asFullString()); // as for php 7.1 defaul null and nullable type is same so proxy can extend target class
	 }
}
