<?php

declare(strict_types = 1);

namespace App\Entity\Contracts;

/**
 * Interface NotificationContract
 * @package App\Entity\Contracts
 */
interface NotificationContract
{
	/**
	 * Generate a textual representation of the notification.
	 * @return string
	 */
	public function getText(): string;
}