<?php

declare(strict_types = 1);

namespace App\Tests\_services\Sanitizer;

use App\Entity\Concerns\ProfanityConcern;
use App\Entity\Concerns\SanitizableConcern;

/**
 * Class SanitizerService
 * @package App\Security\Sanitizer
 */
class SanitizerService
{
	use ProfanityConcern, SanitizableConcern;
	
	/**
	 * @param  string  $dirty
	 * @return string
	 */
	public function sanitize(string $dirty): string
	{
		return $this->cleanString(
			$this->sanitize($dirty)
		);
	}
	
	/**
	 * @param  string  $dirty
	 * @return string
	 */
	public function cleanse(string $dirty): string
	{
		return $this->cleanString(
			$this->purify($dirty)
		);
	}
}