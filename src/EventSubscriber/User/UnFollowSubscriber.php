<?php

declare(strict_types = 1);

namespace App\EventSubscriber\User;

use App\Event\User\UnFollowEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UnFollowSubscriber
 * @package App\Event\User
 */
class UnFollowSubscriber implements EventSubscriberInterface
{
	/**
	 * @return array|\array[][]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			UnFollowEvent::class => [
				['unFollow', 999],
			],
		];
	}
	
	/**
	 * @param  UnFollowEvent  $event
	 */
	public function unFollow(UnFollowEvent $event): void
	{
		$unFollowed = $event->getUnFollowed();
		$unFollower = $event->getUnFollower();
		
		if (! $unFollowed->getFollowers()->contains($unFollower)) {
			
			return;
		}
		
		$unFollower->unFollow($unFollowed);
	}
}