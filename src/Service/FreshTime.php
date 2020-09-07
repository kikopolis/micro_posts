<?php

declare(strict_types = 1);

namespace App\Service;

use DateTime;
use DateTimeInterface;

/**
 * @codeCoverageIgnore
 * Class FreshTime
 * @package App\Service
 */
class FreshTime
{
	/**
	 * @return DateTimeInterface
	 */
	public static function freshTimestamp(): DateTimeInterface
	{
		return new DateTime('now');
	}
}