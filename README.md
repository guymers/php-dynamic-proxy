
A dynamic proxy generator for PHP.

Loosely based on Javassist [ProxyFactory](http://www.csg.is.titech.ac.jp/~chiba/javassist/html/javassist/util/proxy/ProxyFactory.html "ProxyFactory")

# Usage #

```php
Config::set(["CACHE_DIRECTORY" => "/tmp"]);

$class = new ReflectionClass('Class');
$methodOverrides = [
	new MethodHook {
		public function supports(ReflectionMethod $method) {
			return $method->getName() == "test";
		}
	
		public function invoke($proxy, callable $method, array $args) {
			// before original method
	
			$returnValue = $method();
			
			// after original method
			
			return $returnValue
		}
	}
];

$proxy = ProxyFactory::create($class, $methodOverrides);
```
