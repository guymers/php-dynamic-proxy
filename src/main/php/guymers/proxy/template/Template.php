<?php

namespace guymers\proxy\template;

/**
 * Basic templating
 */
class Template {

	/**
	 * Takes a string and replaces {key} with the value from the passed map.
	 *
	 * If the key does not exist in the map, no substitution is done.
	 *
	 * @param string $template
	 * @param array $data
	 * @return string
	 */
	public static function render($template, array $data) {
		$callback = function($matches) use ($data) {
			$key = $matches[2];

			return isset($data[$key]) ? $data[$key] : $matches[1];
		};

		return preg_replace_callback("#(\{(\w+)\})#", $callback, $template);
	}

}
