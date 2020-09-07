<?php

declare(strict_types = 1);

namespace App\EventSubscriber;

use App\Entity\Contracts\AuthorableContract;
use App\Event\AuthorableCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class AuthorableCreatedSubscriber
 * @package App\EventSubscriber
 */
class AuthorableCreatedSubscriber implements EventSubscriberInterface
{
	/**
	 * @return array
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			AuthorableCreatedEvent::class => ['setAuthor', 999],
		];
	}
	
	/**
	 * @param  AuthorableCreatedEvent  $event
	 */
	public function setAuthor(AuthorableCreatedEvent $event): void
	{
		$authorable = $event->getAuthorable();
		
		if ($authorable->getAuthor() === null && $authorable instanceof AuthorableContract) {
			
			$authorable->setAuthor($event->getAuthor());
		}
	}
}