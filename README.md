
A dynamic proxy generator for PHP.

Based on Javassist [ProxyFactory](http://www.csg.is.titech.ac.jp/~chiba/javassist/html/javassist/util/proxy/ProxyFactory.html "ProxyFactory")

# Usage #

```php
Config::set(["CACHE_DIRECTORY" => "/tmp/php-dynamic-proxy"]);

$class = new ReflectionClass("Class");
$methodOverrides = [
	new MethodHook {
		public function supports(ReflectionMethod $method) {
			return $method->getName() == "test";
		}

		public function invoke($proxy, ReflectionMethod $method, array $args) {
			// before original method

			$returnValue = $method->invokeArgs($proxy, $args);

			// after original method

			return $returnValue;
		}
	}
];

$proxy = ProxyFactory::create($class, $methodOverrides);
```
