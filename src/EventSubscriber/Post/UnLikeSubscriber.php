<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Post;

use App\Event\Post\UnLikeEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UnLikeSubscriber
 * @package App\EventSubscriber\Post
 */
class UnLikeSubscriber implements EventSubscriberInterface
{
	/**
	 * @return array|\array[][]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			UnLikeEvent::class => ['unLike', 999],
		];
	}
	
	/**
	 * @param   UnLikeEvent   $event
	 */
	public function unLike(UnLikeEvent $event): void
	{
		$post      = $event->getPost();
		$unlikedBy = $event->getUnlikedBy();
		
		if ($post->getAuthor()->getId() !== $unlikedBy->getId()
			&& $post->getLikedBy()->contains($unlikedBy)) {
			
			$post->unlike($unlikedBy);
		}
	}
}