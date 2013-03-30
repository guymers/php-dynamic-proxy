<?php

namespace guymers\proxy\template;

use \PHPUnit_Framework_TestCase;

class TemplateTest extends PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function template() {
		$template = "
			{test}
			{test2}
			{test_3}
		";

		$data = [
			"test" => "value",
			"test_3" => "value 3"
		];

		$result = Template::render($template, $data);

		$expected = "
			value
			{test2}
			value 3
		";

		$this->assertEquals($expected, $result);
	}

}
