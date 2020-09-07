<?php

declare(strict_types = 1);

namespace App\Event\Notification;

use App\Entity\Notification;
use Symfony\Contracts\EventDispatcher\Event;

class MassReadEvent extends Event
{
	const NAME = 'mass.notification.read';
	
	private array $notifications;
	
	/**
	 * MassReadEvent constructor.
	 * @param  array|Notification[]  $notifications
	 */
	public function __construct(array $notifications)
	{
		$this->notifications = $notifications;
	}
	
	/**
	 * @return array|Notification[]
	 */
	public function getNotifications(): array
	{
		return $this->notifications;
	}
}