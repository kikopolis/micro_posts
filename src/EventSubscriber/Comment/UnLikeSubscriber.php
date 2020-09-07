<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Comment;

use App\Event\Comment\UnLikeEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UnLikeSubscriber
 * @package App\EventSubscriber\Comment
 */
class UnLikeSubscriber implements EventSubscriberInterface
{
	
	/**
	 * @return array|\array[][]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			UnLikeEvent::class => ['unlike', 999],
		];
	}
	
	/**
	 * @param   UnLikeEvent   $event
	 */
	public function unlike(UnLikeEvent $event): void
	{
		$comment   = $event->getComment();
		$unlikedBy = $event->getUnLikedBy();
		
		if ($comment->getAuthor()->getId() !== $unlikedBy->getId()
			&& $comment->getLikedBy()->contains($unlikedBy)) {
			
			$comment->unlike($unlikedBy);
		}
	}
}