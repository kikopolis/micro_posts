<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Post;

use App\Event\Post\ViewEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ViewSubscriber
 * @package App\EventSubscriber\Post
 */
class ViewSubscriber implements EventSubscriberInterface
{
	/**
	 * @return array|array[]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			ViewEvent::class => ['increment', 999],
		];
	}
	
	/**
	 * @param  ViewEvent  $event
	 */
	public function increment(ViewEvent $event): void
	{
		$event->getPost()->incrementViewCounters();
	}
}