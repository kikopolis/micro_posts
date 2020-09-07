<?php

declare(strict_types = 1);

namespace App\EventSubscriber\User;

use App\Event\User\RestoreEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class RestoreSubscriber
 * @package App\EventSubscriber\User
 */
class RestoreSubscriber implements EventSubscriberInterface
{
	/**
	 * @return array[]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			RestoreEvent::class => ['restore', 999],
		];
	}
	
	/**
	 * @param  RestoreEvent  $event
	 */
	public function restore(RestoreEvent $event): void
	{
		$user = $event->getRestored();
		
		$user->restore();
		$user->setRestoredBy($event->getRestoredBy());
	}
}