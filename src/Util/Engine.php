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

	private static function replaceFunctions() // FIXME - Refactor this function.

	{
		preg_match_all('/\{\{\s?\?(?<name>\w+)\s(?<parameter>\w+)\s?\}\}(?<content>.*?)\{\{\s?\?\/\1\s?\}\}/s', self::$view, $matches, PREG_SET_ORDER);

		$replacements = [];

		foreach ($matches as $match) {
			$pattern = $match[0];

			$parameter = (int)$match['parameter'] != 0 ? (int)$match['parameter'] : self::$data[$match['parameter']] ?? null;

			if (!is_null($parameter)) {
				$replace = call_user_func(array(self::class , $match['name']), $parameter, $match['content']);
				$replacements[$pattern] = $replace;
			}
		}

		self::$view = str_replace(array_keys($replacements), array_values($replacements), self::$view); // FIXME - Change it to preg_replace;
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

	private static function setForeachPattern(string $value): string
	{
		return "/\{\{\s?this\.{$value}\s?\}\}/m";
	}

	private static function foreach (array $array, string $content): string
	{
		preg_match_all(self::setForeachPattern('(?<name>\w+)'), $content, $contentMatches, PREG_SET_ORDER);

		$newContent = '';

		foreach ($array as $key => $value) {
			$replacement = [
				self::setForeachPattern('index') => $key + 1
			];

			if (!is_array($value)) {
				$replacement[self::setForeachPattern('value')] = $value;
			}
			else {
				foreach ($contentMatches as $var) {
					$pattern = self::setForeachPattern($var['name']) ?? null;
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

	private static function repeat(int $parameter, string $content)
	{
		$newContent = '';

		for ($i = 1; $i <= $parameter; $i++) {
			$newContent .= preg_replace('/\{\{\s?this\.index\s?\}\}/m', (string)$i, $content);
		}

		return $newContent;
	}
}

// NOTE - /(\{\{\s?\?(\w+)\s(\w+)\s?\}\}(.*?)\{\{\s?\?\/(\w+)\s?\}\})/s
// NOTE - /(?<globalScope>\{\{\s?\?(?<name>\w+)\s(?<var>\w+)\s?\}\}(?<content>.*?)\{\{\s?\?\/(?<functionEnd>\w+)\s?\}\})/sg
// NOTE - /(\{\{\s?\?(\w+)\s(\w+)\s?\}\}(.*?)\{\{\s?\?\/(\2)\s?\}\})/sg