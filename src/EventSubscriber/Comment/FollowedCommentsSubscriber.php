<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Comment;

use App\Entity\Notification\FollowedUserCommentsNotification;
use App\Event\Comment\FollowedUserCommentsEvent;
use App\Event\TimeStampableCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class FollowedCommentsSubscriber
 * @package App\EventSubscriber\Comment
 */
class FollowedCommentsSubscriber implements EventSubscriberInterface
{
	/**
	 * @var EntityManagerInterface
	 */
	private EntityManagerInterface $entityManager;
	
	/**
	 * @var EventDispatcherInterface
	 */
	private EventDispatcherInterface $eventDispatcher;
	
	/**
	 * FollowedUserCommentsSubscriber constructor.
	 * @param  EntityManagerInterface    $entityManager
	 * @param  EventDispatcherInterface  $eventDispatcher
	 */
	public function __construct(
		EntityManagerInterface $entityManager,
		EventDispatcherInterface $eventDispatcher
	)
	{
		$this->entityManager   = $entityManager;
		$this->eventDispatcher = $eventDispatcher;
	}
	
	/**
	 * @return array|array[]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			FollowedUserCommentsEvent::class => ['notify', 999],
		];
	}
	
	/**
	 * @param  FollowedUserCommentsEvent  $event
	 */
	public function notify(FollowedUserCommentsEvent $event): void
	{
		$comment = $event->getComment();
		
		foreach ($event->getFollowers() as $follower) {
			$note = new FollowedUserCommentsNotification(
				$follower,
				$comment,
				$comment->getAuthor()
			);
			
			$this->eventDispatcher->dispatch(
				new TimeStampableCreatedEvent($note)
			);
			
			$this->entityManager->persist($note);
		}
	}
}