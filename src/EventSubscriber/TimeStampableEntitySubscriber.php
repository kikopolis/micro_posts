<?php

declare(strict_types = 1);

namespace App\EventSubscriber;

use App\Entity\Contracts\TimeStampableContract;
use App\Event\TimeStampableCreatedEvent;
use App\Event\TimeStampableUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TimeStampableEntitySubscriber implements EventSubscriberInterface
{
	/**
	 * @return array
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			TimeStampableCreatedEvent::class => ['setCreationStamp', 999],
			TimeStampableUpdatedEvent::class => ['setUpdateStamp', 999],
		];
	}
	
	/**
	 * @param  TimeStampableCreatedEvent  $event
	 */
	public function setCreationStamp(TimeStampableCreatedEvent $event): void
	{
		$stampable = $event->getTimeStampable();
		
		if ($stampable->getCreatedAt() === null && $stampable->hasTimestamps() && $stampable instanceof TimeStampableContract) {
			
			$stampable->setCreationTimestamps();
		}
	}
	
	/**
	 * @param  TimeStampableUpdatedEvent  $event
	 */
	public function setUpdateStamp(TimeStampableUpdatedEvent $event): void
	{
		$stampable = $event->getTimeStampable();
		
		if ($stampable->getUpdatedAt() === null && $stampable->hasTimestamps() && $stampable instanceof TimeStampableContract) {
			
			$stampable->setUpdatedTimestamps();
		}
	}
}