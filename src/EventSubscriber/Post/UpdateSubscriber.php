<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Post;

use App\Event\Post\UpdateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UpdateSubscriber
 * @package App\EventSubscriber\Post
 */
class UpdateSubscriber implements EventSubscriberInterface
{
	/**
	 * @return array[]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			UpdateEvent::class => ['unApprove', 999],
		];
	}
	
	/**
	 * @param  UpdateEvent  $event
	 */
	public function unApprove(UpdateEvent $event): void
	{
		$event->getPost()->unApprove();
	}
}