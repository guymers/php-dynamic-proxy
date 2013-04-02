<?php

namespace guymers\proxy\internal;

use \PHPUnit_Framework_TestCase;
use \ReflectionClass;

use guymers\proxy\mock\Test;

class ParameterTest extends PHPUnit_Framework_TestCase {

	private $parameterClassAndReference;
	private $parameterClassAndDefault;
	private $parameterClassNotLoaded;

	public function setUp() {
		$test = new Test("");
		$class = new ReflectionClass($test);
		$method = $class->getMethod("testingTypeHintParams");
		$parameters = $method->getParameters();

		$parameter = $parameters[0];
		$this->parameterClassAndReference = new Parameter($parameter);

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
		$this->assertEquals('$param1', $this->parameterClassAndReference->asString());
		$this->assertEquals('$param2', $this->parameterClassAndDefault->asString());
		$this->assertEquals('$param', $this->parameterClassNotLoaded->asString());
	}

	/**
	 * @test
	 */
	public function asFullString() {
		$this->assertEquals('\guymers\proxy\mock\Test &$param1', $this->parameterClassAndReference->asFullString());
		$this->assertEquals('\guymers\proxy\mock\Test $param2 = NULL', $this->parameterClassAndDefault->asFullString());
		$this->assertEquals('\guymers\proxy\blah\TestNotLoaded $param', $this->parameterClassNotLoaded->asFullString());
	}

}
