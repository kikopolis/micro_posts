<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Post;

use App\Entity\Notification\FollowedUserPostsNotification;
use App\Event\Post\FollowedUserPostsEvent;
use App\Event\TimeStampableCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class FollowedPostSubscriber implements EventSubscriberInterface
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
	 * FollowedUserPostsSubscriber constructor.
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
	 * @return array
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			FollowedUserPostsEvent::class => ['notify', 999],
		];
	}
	
	/**
	 * @param  FollowedUserPostsEvent  $event
	 */
	public function notify(FollowedUserPostsEvent $event): void
	{
		$post = $event->getPost();
		
		foreach ($event->getFollowers() as $follower) {
			$note = new FollowedUserPostsNotification(
				$follower,
				$post,
				$post->getAuthor()
			);
			
			$this->eventDispatcher->dispatch(
				new TimeStampableCreatedEvent($note)
			);
			
			$this->entityManager->persist($note);
		}
	}
}