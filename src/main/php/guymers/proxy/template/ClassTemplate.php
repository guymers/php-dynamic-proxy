<?php

namespace guymers\proxy\template;

use \ReflectionClass;

use guymers\proxy\Config;

class ClassTemplate {

	private static $TEMPLATE = '
		{namespace}

		final class {proxyClassName} extends {className} implements \{proxyInterface} {

			private $_methodHooks = [];

			public function _setMethodHooks(array $methodHooks) {
				$this->_methodHooks = $methodHooks;
			}

			{methods}
		}
	';

	/**
	 * @var ReflectionClass
	 */
	private $class;

	public function __construct(ReflectionClass $class) {
		$this->class = $class;
	}

	public function render($proxyClassName, array $methodNames) {
		$methodStrings = [];

		foreach ($methodNames as $methodName) {
			$method = $this->class->getMethod($methodName);
			$methodTemplate = new MethodTemplate($method);

			$methodStrings[] = $methodTemplate->render();
		}

		$data = [
			"namespace" => $this->getNamespaceString(),
			"className" => $this->class->getShortName(),
			"proxyClassName" => $proxyClassName,
			"proxyInterface" => Config::$PROXY_IMPLEMENTATION,
			"methods" => join("\n\n", $methodStrings)
		];

		return Template::render(self::$TEMPLATE, $data);
	}

	private function getNamespaceString() {
		$string = "";
		$namespace = $this->class->getNamespaceName();

		if ($namespace) {
			$string = "namespace $namespace;";
		}

		return $string;
	}

}
