<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Comment;

use App\Event\Comment\UpdateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UpdateSubscriber
 * @package App\EventSubscriber\Comment
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
		$event->getComment()->unApprove();
	}
}