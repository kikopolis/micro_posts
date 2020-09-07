<?php

declare(strict_types = 1);

namespace App\Controller\Concerns;

use Symfony\Component\HttpFoundation\Request;

/**
 * Trait ExpectsJsonConcern
 * @package App\Controller\Concerns
 */
trait ExpectsJsonConcern
{
	/**
	 * Check if the request was made with async JS.
	 * @param  Request  $request
	 * @return bool
	 */
	protected function expectsJson(Request $request): bool
	{
		return $request->headers->contains('X-Requested-With', 'XMLHttpRequest');
	}
}