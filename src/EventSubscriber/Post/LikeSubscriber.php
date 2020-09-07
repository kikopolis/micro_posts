<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Post;

use App\Entity\Notification\PostLikeNotification;
use App\Event\Post\LikeEvent;
use App\Event\TimeStampableCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class LikeSubscriber
 * @package App\EventSubscriber\Post
 */
class LikeSubscriber implements EventSubscriberInterface
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
	 * PostLikedSubscriber constructor.
	 * @param   EntityManagerInterface     $entityManager
	 * @param   EventDispatcherInterface   $eventDispatcher
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
			LikeEvent::class => [
				['like', 999],
				['notify', 997],
			],
		];
	}
	
	public function like(LikeEvent $event): void
	{
		$post  = $event->getPost();
		$liker = $event->getLiker();
		
		if ($post->getAuthor()->getId() !== $liker->getId()
			&& ! $post->getLikedBy()->contains($liker)) {
			
			$post->like($liker);
		}
	}
	
	/**
	 * @param   LikeEvent   $event
	 */
	public function notify(LikeEvent $event): void
	{
		$post = $event->getPost();
		
		$note = new PostLikeNotification(
			$post->getAuthor(),
			$post,
			$event->getLiker()
		);
		
		$this->eventDispatcher->dispatch(
			new TimeStampableCreatedEvent($note)
		);
		
		$this->entityManager->persist($note);
	}
}