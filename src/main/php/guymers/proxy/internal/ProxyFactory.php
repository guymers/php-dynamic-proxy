<?php

namespace guymers\proxy\internal;

use \Exception;
use \ReflectionClass;
use \ReflectionMethod;
use \ReflectionParameter;

use guymers\proxy\Config;
use guymers\proxy\MethodAndHook;
use guymers\proxy\exception\AlreadyProxyException;
use guymers\proxy\exception\HookAlreadyDefinedException;
use guymers\proxy\template\ClassTemplate;
use guymers\proxy\template\MethodTemplate;

class ProxyFactory {

	/**
	 * @var ReflectionClass
	 */
	private $class;

	/**
	 * @var MethodHook[]
	 */
	private $methodHooks;

	/**
	 * @var string
	 */
	private $proxyClassShortName;

	public function __construct(ReflectionClass $class, array $methodHooks) {
		$this->class = $class;
		$this->methodHooks = $methodHooks;
		$this->proxyClassShortName = $this->getProxyClassName();
	}

	private function getProxyClassName() {
		$className = $this->class->getName();
		$fileName = $this->class->getFileName();
		$modificationTime = filemtime($fileName);
		$uniqueIdentifier = $className . "_" . $modificationTime;

		foreach ($this->methodHooks as $methodHook) {
			$class = new ReflectionClass($methodHook);
			$className = $class->getName();
			$fFileName = $class->getFileName();
			$modificationTime = filemtime($fFileName);

			$uniqueIdentifier .= "_" . $className . "_" . $modificationTime;
		}

		$uniqueIdentifier = md5($uniqueIdentifier);

		$proxyClassName = $this->class->getShortName();
		$proxyClassName = "Proxy__" . $proxyClassName . "_" . $uniqueIdentifier;

		return $proxyClassName;
	}

	public function create(array $args = null) {
		if ($this->class->implementsInterface(Config::$PROXY_IMPLEMENTATION)) {
			throw new AlreadyProxyException();
		}

		$fileName = $this->getProxyClassFileName();
		$methodHooksByName = $this->getMethodHooksByName();

		if (!file_exists($fileName)) {
			$methodNames = array_keys($methodHooksByName);

			$classTemplate = new ClassTemplate($this->class);
			$classString = $classTemplate->render($this->proxyClassShortName, $methodNames);

			file_put_contents($fileName, "<?php\n" . $classString);
			chmod($fileName, Config::$CACHE_FILE_PERMISSIONS);
		}

		$namespace = $this->class->getNamespaceName();
		$namespace = $namespace ? "\\$namespace\\" : "";
		$proxyClassName = $namespace . $this->proxyClassShortName;
		$proxyClass = new ReflectionClass($proxyClassName);

		if (is_null($args)) {
			$proxy = $proxyClass->newInstanceWithoutConstructor();
		} else {
			$proxy = $proxyClass->newInstanceArgs($args);
		}

		$proxy->_setMethodHooks($methodHooksByName);

		return $proxy;
	}

	private function getProxyClassFileName() {
		$directory = $this->getProxyCacheDirectory();
		$fileExtension = Config::$FILE_EXTENSION;
		$filename = $directory . DIRECTORY_SEPARATOR . $this->proxyClassShortName . "." . $fileExtension;

		return $filename;
	}

	private function getProxyCacheDirectory() {
		$namespace = $this->class->getNamespaceName();
		$namespace = str_replace("\\", DIRECTORY_SEPARATOR, $namespace);
		$directory = Config::$CACHE_DIRECTORY . DIRECTORY_SEPARATOR . $namespace;

		if (!is_dir($directory)) {
			$old = umask(0);
			mkdir($directory, Config::$CACHE_DIRECTORY_PERMISSIONS, true);
			umask($old);
		}

		return $directory;
	}

	private function getMethodHooksByName() {
		$methodHooksByName = [];

		$methods = $this->class->getMethods(ReflectionMethod::IS_PUBLIC);

		foreach ($methods as $method) {
			$methodName = $method->getName();
			$methodHooksForMethod = $this->getMethodHooksForMethod($method);

			if (count($methodHooksForMethod) > 1) {
				throw new HookAlreadyDefinedException("more than one hook for method $methodName");
			}

			if (isset($methodHooksForMethod[0])) {
				$hook = $methodHooksForMethod[0];
				$methodHooksByName[$methodName] = new MethodAndHook($method, $hook);
			}
		}

		return $methodHooksByName;
	}

	private function getMethodHooksForMethod(ReflectionMethod $method) {
		$methodHooksForMethod = [];

		foreach ($this->methodHooks as $methodHook) {
			if ($methodHook->supports($method)) {
				$methodHooksForMethod[] = $methodHook;
			}
		}

		return $methodHooksForMethod;
	}

}
