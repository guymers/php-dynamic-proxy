<?php

namespace guymers\proxy\template;

class Template {

	public static function render($template, $data) {
		$callback = function($matches) use ($data) {
			$key = $matches[1];

			return $data[$key];
		};

		return preg_replace_callback("#\{(\w+)\}#", $callback, $template);
	}

}
