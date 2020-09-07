<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Notification;

use App\Event\Notification\ReadEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ReadSubscriber implements EventSubscriberInterface
{
	/**
	 * @return array|array[]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			ReadEvent::class => ['read', 999],
		];
	}
	
	/**
	 * @param  ReadEvent  $event
	 */
	public function read(ReadEvent $event): void
	{
		$event->getNotification()->setSeen(true);
	}
}