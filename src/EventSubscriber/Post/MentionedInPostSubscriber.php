<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Post;

use App\Entity\Notification\UserMentionedInPostNotification;
use App\Entity\Post;
use App\Entity\User;
use App\Event\Post\MentionedInPostEvent;
use App\Event\TimeStampableCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class MentionedInPostSubscriber
 * @package App\EventSubscriber\Post
 */
class MentionedInPostSubscriber implements EventSubscriberInterface
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
			MentionedInPostEvent::class => [
				['notify', 999],
			],
		];
	}
	
	/**
	 * @param  MentionedInPostEvent  $event
	 */
	public function notify(MentionedInPostEvent $event): void
	{
		$post     = $event->getPost();
		$postedBy = $post->getAuthor();
		
		foreach ($event->getTaggedUsers() as $taggedUser) {
			
			$this->note($post, $taggedUser, $postedBy);
		}
	}
	
	/**
	 * @param  Post  $post
	 * @param  User  $taggedUser
	 * @param  User  $postedBy
	 */
	protected function note(Post $post, User $taggedUser, User $postedBy)
	{
		$note = new UserMentionedInPostNotification(
			$taggedUser,
			$post,
			$postedBy
		);
		
		$this->eventDispatcher->dispatch(
			new TimeStampableCreatedEvent($note)
		);
		
		$this->entityManager->persist($note);
	}
}