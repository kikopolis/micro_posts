<?php

declare(strict_types = 1);

namespace App\Kikopolis;

/**
 * Class Str
 * @package App\Kikopolis
 */
class Str
{
	/**
	 * Generate a pseudo random string.
	 * @param  integer  $length
	 * @return  string
	 * @throws \Exception random_bytes throws \Exception if no sufficient entropy was gathered.
	 */
	public static function random(int $length = 16): string
	{
		$string = '';
		while (($len = strlen($string) < $length)) {
			$size   = $length - $len;
			$bytes  = random_bytes($size);
			$string .= mb_substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
		}
		
		return $string;
	}
	
	/**
	 * Convert a string to url friendly slug format.
	 * @param  string  $string
	 * @param  string  $separator
	 * @return string
	 */
	public static function slug(string $string, string $separator = '-'): string
	{
		// Convert underscores into separator
		$flip   = $separator === '-' ? '_' : '-';
		$string = preg_replace('![' . preg_quote($flip) . ']+!u', $separator, $string);
		// Replace @ with at
		$string = str_replace('@', $separator . 'at' . $separator, $string);
		// Replace & with and
		$string = preg_replace('/&/', $separator . 'and' . $separator, $string);
		// Replace % with percentage
		$string = preg_replace('/%/', $separator . 'percentage' . $separator, $string);
		// Remove all chars that are not whitespace, separator, letters or numbers
		$string = preg_replace('![^' . preg_quote($separator) . '\pL\pN\s]+!u', '', strtolower($string));
		// Replace all whitespace and separator with single separator
		$string = preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $string);
		
		return trim($string, $separator);
	}
	
	/**
	 * @param  string  $string
	 * @param  int     $flags
	 * @param  string  $encoding
	 * @return string
	 */
	public static function h(string $string, int $flags = ENT_QUOTES, $encoding = 'UTF-8'): string
	{
		return htmlspecialchars($string, $flags, $encoding);
	}
	
	/**
	 * Limit word count in a string to the optional specified number and append the optional parameter '...'.
	 * @param  string   $string
	 * @param  integer  $limit   Optional word limit.
	 * @param  string   $append  Option append to the end of string to signify there is more content.
	 * @return  string
	 */
	public static function limit(string $string, int $limit = 20, string $append = '...'): string
	{
		preg_match('/^\s*+(?:\S++\s*+){1,' . $limit . '}/u', $string, $matches);
		if (! $matches[0] || mb_strlen($string) === mb_strlen($matches[0])) {
			
			return $string;
		}
		
		return rtrim($matches[0]) . $append;
	}
	
	/**
	 * Limit letters in a string to a default of 60 or specify a limit yourself.
	 * @param  string  $string
	 * @param  int     $limit
	 * @return string
	 */
	public static function limitLetters(string $string, int $limit = 60): string
	{
		return mb_strcut($string, 0, $limit);
	}
	
	/**
	 * Trim the passed char from the end and beginning of string.
	 * @param  string  $trim
	 * @param  string  $string
	 * @return string
	 */
	public static function trim(string $trim, string $string): string
	{
		return rtrim(
			ltrim($string, $trim),
			$trim
		);
	}
}