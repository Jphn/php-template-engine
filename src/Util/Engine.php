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

		$path = __DIR__ . "/../View/{$view}.html";

		if (!file_exists($path))
			return self::$view;

		self::$view = file_get_contents($path);

		self::replaceFunctions();
		self::replaceVars();

		return self::$view;
	}

	private static function replaceVars()
	{
		$keys = array_map(function ($item) {
			return "/\{\{\s?{$item}\s?\}\}/m";
		}, array_keys(self::$data));
		self::$view = preg_replace($keys, array_values(self::$data), self::$view);
	}

	private static function replaceFunctions()
	{
		preg_match_all('/\{\{\s?\?(?<name>\w+)\s(?<parameter>\w+)\s?\}\}(?<content>.*?)\{\{\s?\?\/\1\s?\}\}/s', self::$view, $matches, PREG_SET_ORDER);

		if (count($matches) == 0)
			return;

		$replacements = [];

		foreach ($matches as $match) {
			$pattern = $match[0];

			$parameter = self::$data[$match['parameter']];
			unset(self::$data[$match['parameter']]);

			$replace = call_user_func(array(self::class , $match['name']), $parameter, $match['content']);

			$replacements[$pattern] = $replace;
		}

		self::$view = str_replace(array_keys($replacements), array_values($replacements), self::$view);
	}

	private static function foreach (array $array, string $content): string
	{

		preg_match_all('/\{\{\s?(?<name>\w+)\s?\}\}/m', $content, $contentMatches, PREG_SET_ORDER);

		$newContent = '';

		foreach ($array as $value) {
			$replacement = [];

			foreach ($contentMatches as $var) {
				$pattern = $var[0];
				$replace = $value[$var['name']];
				$replacement[$pattern] = $replace;
			}

			$newContent .= str_replace(array_keys($replacement), array_values($replacement), $content);
		}

		return $newContent;
	}

	private static function if ()
	{
		return 'IF FUNCTION';
	}
}

// NOTE - /(\{\{\s?\?(\w+)\s(\w+)\s?\}\}(.*?)\{\{\s?\?\/(\w+)\s?\}\})/s
// NOTE - /(?<globalScope>\{\{\s?\?(?<name>\w+)\s(?<var>\w+)\s?\}\}(?<content>.*?)\{\{\s?\?\/(?<functionEnd>\w+)\s?\}\})/sg
// NOTE - /(\{\{\s?\?(\w+)\s(\w+)\s?\}\}(.*?)\{\{\s?\?\/(\2)\s?\}\})/sg