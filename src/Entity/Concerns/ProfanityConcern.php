<?php

declare(strict_types = 1);

namespace App\Entity\Concerns;

use Crudle\Profanity\Dictionary\GB;
use Crudle\Profanity\Dictionary\US;
use Crudle\Profanity\Filter;

/**
 * Trait ProfanityConcern
 * @package App\Entity\Concerns
 */
trait ProfanityConcern
{
	/**
	 * Clean the string from html and profanities. If contains, then cleans.
	 * @param  string  $text
	 * @return string
	 */
	public function cleanString(string $text): string
	{
		if (! $this->containsProfanities($text)) {
			
			return $text;
		}
		
		return $this->filterProfanities(
			$text
		);
	}
	
	/**
	 * Cleans the string of profanities. Does not validate, just cleans.
	 * @param  string  $text
	 * @return string
	 */
	public function filterProfanities(string $text): string
	{
		foreach ($this->profanityDictionaries() as $dict) {
			
			$text = (new Filter($dict))->cleanse($text);
		}
		
		return $text;
	}
	
	/**
	 * Determine if the string contains profanities. Only checks, does not cleanse.
	 * @param  string  $text
	 * @return bool
	 */
	public function containsProfanities(string $text): bool
	{
		foreach ($this->profanityDictionaries() as $dict) {
			
			if ((new Filter($dict))->isDirty($text)) {
				
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Return custom filters together with defaults or just the defaults.
	 * @return array
	 */
	public function profanityDictionaries(): array
	{
		$defaults = [new GB(), new US()];
		
		return defined('static::PROFANITY_FILTERS') ? array_merge(static::PROFANITY_FILTERS, $defaults) : $defaults;
	}
}