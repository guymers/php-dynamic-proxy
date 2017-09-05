<?php

namespace guymers\proxy\template;

use \ReflectionMethod;

use guymers\proxy\internal\Parameter;

/**
 * Template for a method
 */
class MethodTemplate {

	private static $TEMPLATE = '
		public function {method}({parameterDefinitions}) {returnType}{
			$methodAndHook = $this->_methodHooks["{method}"];
			$method = $methodAndHook->getMethod();
			$hook = $methodAndHook->getHook();
			$args = func_get_args();

			return $hook->invoke($this, $method, $args);
		}
	';

	/**
	 * @var ReflectionMethod
	 */
	private $method;

	public function __construct(ReflectionMethod $method) {
		$this->method = $method;
	}

	public function render() {
		$parameters = [];
		$parameterDefinitions = [];

		foreach ($this->method->getParameters() as $parameter) {
			$internalParameter = new Parameter($parameter);

			$parameters[] = $internalParameter->asString();
			$parameterDefinitions[] = $internalParameter->asFullString();
		}

		$type = "";

		if (version_compare(PHP_VERSION, '7.0.0', '>=') > 0 && $this->method->hasReturnType()) {
			$returnType = $this->method->getReturnType();
			$type = "\\".$returnType->getName();
			if (version_compare(PHP_VERSION, '7.1.0', '>=') > 0 && $returnType->allowsNull()) {
				$type = "?".$type;
			}
			$type = ": ".$type;
		}

		$data = [
			"method" => $this->method->getName(),
			"parameters" => join(", ", $parameters),
			"parameterDefinitions" => join(", ", $parameterDefinitions),
			"returnType" => $type
		];

		return Template::render(self::$TEMPLATE, $data);
	}

}
