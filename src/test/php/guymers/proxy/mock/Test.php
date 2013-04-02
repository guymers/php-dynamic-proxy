<?php

namespace guymers\proxy\mock;

class Test {

	private $constructorVariable = "";
	private $variable = "";

	public function __construct($constructorVariable) {
		$this->constructorVariable = $constructorVariable;
	}

	public function getConstructorVariable() {
		return $this->constructorVariable;
	}

	public function getVariable() {
		return $this->variable;
	}

	public function setVariable($variable) {
		$this->variable = $variable;
	}


	public function testing() {
		return "blah";
	}

	public function testingParams($param1, $param2, $param3 = '', $param4 = 'test', $param5 = 5, $param6 = [], array $param7 = [], $param8 = null, $param9 = true) {
		return "$param1 - $param2 = $param3";
	}

	public function testingTypeHintParams(Test &$param1, Test $param2 = null) {
		return "testingTypeHintParams " . $param1->testingParams("1", "2");
	}

	public function testingTypeHintParamsClassNotLoaded(\guymers\proxy\blah\TestNotLoaded $param) {}

}
