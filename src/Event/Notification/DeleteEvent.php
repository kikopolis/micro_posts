<?php

declare(strict_types = 1);

namespace App\Event\Notification;

use App\Entity\Notification;
use Symfony\Contracts\EventDispatcher\Event;

class DeleteEvent extends Event
{
	const NAME = 'notification.delete';
	
	/**
	 * @var Notification
	 */
	private Notification $notification;
	
	/**
	 * DeleteEvent constructor.
	 * @param  Notification  $notification
	 */
	public function __construct(Notification $notification)
	{
		$this->notification = $notification;
	}
	
	/**
	 * @return Notification
	 */
	public function getNotification(): Notification
	{
		return $this->notification;
	}
}