<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Comment;

use App\Entity\Comment;
use App\Entity\Notification\UserMentionedInCommentNotification;
use App\Entity\User;
use App\Event\Comment\MentionedInCommentEvent;
use App\Event\TimeStampableCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class MentionedInCommentSubscriber
 * @package App\EventSubscriber\Comment
 */
class MentionedInCommentSubscriber implements EventSubscriberInterface
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
	 * MentionedInPostSubscriber constructor.
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
			MentionedInCommentEvent::class => ['notify', 999],
		];
	}
	
	/**
	 * @param  MentionedInCommentEvent  $event
	 */
	public function notify(MentionedInCommentEvent $event): void
	{
		$comment  = $event->getComment();
		$postedBy = $comment->getAuthor();
		
		foreach ($event->getTaggedUsers() as $taggedUser) {
			
			$this->note($taggedUser, $comment, $postedBy);
		}
	}
	
	/**
	 * @param  User     $taggedUser
	 * @param  Comment  $comment
	 * @param  User     $postedBy
	 */
	private function note(User $taggedUser, Comment $comment, User $postedBy): void
	{
		$note = new UserMentionedInCommentNotification(
			$taggedUser,
			$comment,
			$postedBy
		);
		
		$this->eventDispatcher->dispatch(
			new TimeStampableCreatedEvent($note)
		);
		
		$this->entityManager->persist($note);
	}
}