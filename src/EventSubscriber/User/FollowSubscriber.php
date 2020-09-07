<?php

declare(strict_types = 1);

namespace App\EventSubscriber\User;

use App\Entity\Notification\FollowNotification;
use App\Event\TimeStampableCreatedEvent;
use App\Event\User\FollowEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class FollowSubscriber implements EventSubscriberInterface
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
	 * FollowSubscriber constructor.
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
	 * @return array|\array[][]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			FollowEvent::class => ['follow', 999],
		];
	}
	
	/**
	 * @param  FollowEvent  $event
	 */
	public function follow(FollowEvent $event): void
	{
		$followed = $event->getFollowed();
		$follower = $event->getFollower();
		
		if ($followed->getFollowers()->contains($follower)) {
			
			return;
		}
		
		$follower->follow($followed);
		
		$note = new FollowNotification($followed, $follower);
		
		$this->eventDispatcher->dispatch(
			new TimeStampableCreatedEvent($note)
		);
		
		$this->entityManager->persist($note);
	}
}