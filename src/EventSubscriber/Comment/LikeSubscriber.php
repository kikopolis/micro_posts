<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Comment;

use App\Entity\Notification\CommentLikeNotification;
use App\Event\Comment\LikeEvent;
use App\Event\TimeStampableCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class LikeSubscriber
 * @package App\EventSubscriber\Comment
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
	 * LikeSubscriber constructor.
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
	 * @return array|\array[][]
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
	
	/**
	 * @param   LikeEvent   $event
	 */
	public function like(LikeEvent $event): void
	{
		$comment = $event->getComment();
		$liker   = $event->getLiker();
		
		if ($comment->getAuthor()->getId() !== $liker->getId()
			&& ! $comment->getLikedBy()->contains($liker)) {
			
			$comment->like($liker);
		}
	}
	
	/**
	 * @param   LikeEvent   $event
	 */
	public function notify(LikeEvent $event): void
	{
		$comment = $event->getComment();
		
		$note = new CommentLikeNotification(
			$comment->getAuthor(),
			$comment,
			$event->getLiker()
		);
		
		$this->eventDispatcher->dispatch(
			new TimeStampableCreatedEvent($note)
		);
		
		$this->entityManager->persist($note);
	}
}