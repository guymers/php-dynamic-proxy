<?php

namespace guymers\proxy\template;

use \Exception;
use \ReflectionMethod;
use \ReflectionParameter;

class MethodTemplate {

	private static $TEMPLATE = '
		public function {method}({parameterDefinitions}) {
			$methodHook = $this->methodHooks["{method}"];
			$method = function() use ({parameters}) {
				return parent::{method}({parameters});
			};

			return $methodHook->invoke($this, $method, func_get_args());
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
			$parameters[] = $this->getParameterString($parameter);
			$parameterDefinitions[] = $this->getParameterDefinitionString($parameter);
		}

		$data = [
			"method" => $this->method->getName(),
			"parameters" => join(", ", $parameters),
			"parameterDefinitions" => join(", ", $parameterDefinitions)
		];

		return Template::render(self::$TEMPLATE, $data);
	}

	private function getParameterString(ReflectionParameter $parameter) {
		$parameterName = $parameter->getName();
		$parameterString = "\$" . $parameterName;

		return $parameterString;
	}

	private function getParameterDefinitionString(ReflectionParameter $parameter) {
		$parameterString = $this->getParameterString($parameter);

		if ($parameter->isPassedByReference()) {
			$parameterString = "&" . $parameterString;
		}

		$type = $this->getParameterTypeHint($parameter);

		if ($type) {
			$parameterString = $type . " " . $parameterString;
		}

		if ($parameter->isDefaultValueAvailable()) {
			$defaultValue = $parameter->getDefaultValue();
			$defaultValueString = var_export($defaultValue, true);

			$parameterString = $parameterString . " = " . $defaultValueString;
		}

		return $parameterString;
	}

	/**
	 * @see https://gist.github.com/Xeoncross/4723819
	 */
	private function getParameterTypeHint(ReflectionParameter $parameter) {
		if ($parameter->isArray()) {
			return "array";
		}

		$name = "";

		try {
			// first try it on the normal way if the class is loaded then everything should go ok
			$class = $parameter->getClass();

			if ($class) {
				$name = $class->getName();

				if ($class->getNamespaceName()) {
					$name = "\\$name";
				}
			}
		} catch (Exception $exception) {
			// try to resolve it the ugly way by parsing the error message
			$parts = explode(" ", $exception->getMessage(), 3);
			$name = $parts[1];
		}

		return $name;
	}

}
