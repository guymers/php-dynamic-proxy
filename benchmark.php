<?php

use guymers\proxy\ProxyFactory;

require __DIR__ . "/vendor/autoload.php";

use guymers\proxy\MethodHook;
use guymers\proxy\Config;

$iterations = 100;

$cacheDirectory = "/tmp/php-dynamic-proxy-" . md5(microtime());

Config::set(["CACHE_DIRECTORY" => $cacheDirectory]);

class Test {

	public function original($param) {
		return $param;
	}

}

class OriginalInvoke implements MethodHook {

	public function supports(ReflectionMethod $method) {
		return $method->getName() == "original";
	}

	public function invoke($proxy, ReflectionMethod $method, array $args) {
		return $method->invokeArgs($proxy, $args);
	}

}

// http://stackoverflow.com/questions/12966227/#answer-12966487
function deleteDirectory($directory) {
	$directoryIterator = new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS);
	$iterator = new RecursiveIteratorIterator($directoryIterator, RecursiveIteratorIterator::CHILD_FIRST);

	foreach ($iterator as $file) {
		if ($file->isDir()) {
			rmdir($file);
		} else {
			unlink($file);
		}
	}

	rmdir($directory);
}

$createObject = function() {
	return new Test();
};

$createProxy = function() {
	$class = new ReflectionClass("Test");
	$methodHooks = [
		new OriginalInvoke()
	];

	return ProxyFactory::create($class, $methodHooks);
};

function benchmarkCreation($str, $iterations, callable $call, $cacheDirectory = null) {
	$total = 0;

	for ($i = 0; $i < $iterations; $i++) {
		$start = microtime(true);

		$call();

		$end = microtime(true);

		$total = $total + ($end - $start);

		if ($cacheDirectory) {
			deleteDirectory($cacheDirectory);
		}
	}

	$average = $total / $iterations * 1000;

	echo "Average Time taken to create $str: " . $average . " ms\n";
}

benchmarkCreation("object             ", $iterations, $createObject);
benchmarkCreation("proxy with no cache", $iterations, $createProxy, $cacheDirectory);
benchmarkCreation("proxy with cache   ", $iterations, $createProxy);

echo "\n";

function callMethod($str, $iterations, callable $call) {
	$total = 0;

	for ($i = 0; $i < $iterations; $i++) {
		$start = microtime(true);

		$call();

		$end = microtime(true);

		$total = $total + ($end - $start);
	}

	$average = $total / $iterations * 1000;

	echo "Average Time taken to call $str: " . $average . " ms\n";
}

$object = $createObject();

callMethod("object->original", $iterations, function() use ($object) {
	$object->original("blah");
});

$proxy = $createProxy();

callMethod(" proxy->original", $iterations, function() use ($proxy) {
	$proxy->original("blah");
});

deleteDirectory($cacheDirectory);
