<?php

declare(strict_types = 1)
;

namespace App\Util;

abstract class Engine
{
	private static string $view = 'View file not found!';
	private static array $data;

	public static function render(string $view, array $data): string
	{
		self::$data = $data;
		self::$view = $view;

		self::replaceFunctions();
		self::replaceVars();

		return self::$view;
	}

	private static function replaceVars()
	{
		preg_match_all('/\{\{\s?(?<varName>\w+)\s?\}\}/m', self::$view, $matches, PREG_SET_ORDER);

		$replacements = [];

		foreach ($matches as $match) {
			$name = "/\{\{\s?{$match['varName']}\s?\}\}/m";
			$value = self::$data[$match['varName']] ?? null;

			if (isset($value))
				$replacements[$name] = $value;
		}

		self::$view = preg_replace(array_keys($replacements), array_values($replacements), self::$view);
	}

	private static function replaceFunctions() // FIXME - Refactor this function.

	{
		preg_match_all('/\{\{\s?\?(?<name>\w+)\s(?<parameter>\w+)\s?\}\}(?<content>.*?)\{\{\s?\?\/\1\s?\}\}/s', self::$view, $matches, PREG_SET_ORDER);

		$replacements = [];

		foreach ($matches as $match) {
			$pattern = $match[0];

			$parameter = self::$data[$match['parameter']] ?? null;

			$replace = call_user_func(array(self::class , $match['name']), $parameter, $match['content']);

			$replacements[$pattern] = $replace;
		}

		self::$view = str_replace(array_keys($replacements), array_values($replacements), self::$view); // FIXME - Change it to preg_replace;
	}

	private static function foreach (array $array, string $content): string
	{
		function pattern(string $value): string
		{
			return "/\{\{\s?this\.{$value}\s?\}\}/m";
		}

		preg_match_all(pattern('(?<name>\w+)'), $content, $contentMatches, PREG_SET_ORDER);

		$newContent = '';

		foreach ($array as $key => $value) {
			$replacement = [
				pattern('index') => $key + 1
			];

			if (!is_array($value)) {
				$replacement[pattern('value')] = $value;
			}
			else {
				foreach ($contentMatches as $var) {
					$pattern = pattern($var['name']) ?? null;
					$replace = $value[$var['name']] ?? null;

					if (isset($replace, $pattern)) {
						$replacement[$pattern] = strval($replace);
					}
				}
			}

			$newContent .= preg_replace(array_keys($replacement), array_values($replacement), $content);
		}

		return $newContent;
	}

	private static function if (mixed $parameter, string $content)
	{
		if (isset($parameter))
			return $content;
		return '';
	}
}

// NOTE - /(\{\{\s?\?(\w+)\s(\w+)\s?\}\}(.*?)\{\{\s?\?\/(\w+)\s?\}\})/s
// NOTE - /(?<globalScope>\{\{\s?\?(?<name>\w+)\s(?<var>\w+)\s?\}\}(?<content>.*?)\{\{\s?\?\/(?<functionEnd>\w+)\s?\}\})/sg
// NOTE - /(\{\{\s?\?(\w+)\s(\w+)\s?\}\}(.*?)\{\{\s?\?\/(\2)\s?\}\})/sg