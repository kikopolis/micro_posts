<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Notification;

use App\Entity\Notification;
use App\Event\Notification\MassReadEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MassReadSubscriber implements EventSubscriberInterface
{
	/**
	 * @return array|array[]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			MassReadEvent::class => ['read', 999],
		];
	}
	
	/**
	 * @param  MassReadEvent  $event
	 */
	public function read(MassReadEvent $event): void
	{
		foreach ($event->getNotifications() as $notification) {
			
			$this->mark($notification);
		}
	}
	
	/**
	 * @param  Notification  $notification
	 */
	private function mark(Notification $notification): void
	{
		$notification->setSeen(true);
	}
}