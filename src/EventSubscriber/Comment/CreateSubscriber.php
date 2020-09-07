<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Comment;

use App\Entity\Notification\CommentPostedNotification;
use App\Event\Comment\CreateEvent;
use App\Event\TimeStampableCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class CreateSubscriber
 * @package App\EventSubscriber\Comment
 */
class CreateSubscriber implements EventSubscriberInterface
{
	/**
	 * @var EntityManagerInterface
	 */
	private EntityManagerInterface $entityManager;
	
	/**
	 * @var LoggerInterface
	 */
	private LoggerInterface $logger;
	
	/**
	 * @var EventDispatcherInterface
	 */
	private EventDispatcherInterface $eventDispatcher;
	
	/**
	 * CreateSubscriber constructor.
	 * @param  EntityManagerInterface    $entityManager
	 * @param  LoggerInterface           $logger
	 * @param  EventDispatcherInterface  $eventDispatcher
	 */
	public function __construct(
		EntityManagerInterface $entityManager,
		LoggerInterface $logger,
		EventDispatcherInterface $eventDispatcher
	)
	{
		$this->entityManager   = $entityManager;
		$this->logger          = $logger;
		$this->eventDispatcher = $eventDispatcher;
	}
	
	/**
	 * @return array|\array[][]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			CreateEvent::class => [
				['approve', 999],
				['notifyPostAuthor', 998],
			],
		];
	}
	
	/**
	 * @param  CreateEvent  $event
	 */
	public function approve(CreateEvent $event): void
	{
		$comment = $event->getComment();
		
		$comment->unApprove();
		//		$comment->approve();
		//		$comment->setApprovedBy($comment->getAuthor());
	}
	
	/**
	 * @param  CreateEvent  $event
	 */
	public function notifyPostAuthor(CreateEvent $event): void
	{
		$comment = $event->getComment();
		
		$note = new CommentPostedNotification(
			$comment->getPost()->getAuthor(),
			$comment,
			$comment->getAuthor()
		);
		
		$this->eventDispatcher->dispatch(
			new TimeStampableCreatedEvent($note)
		);
		
		$this->entityManager->persist($note);
	}
	
	/**
	 * @param  CreateEvent  $event
	 */
	public function log(CreateEvent $event): void
	{
		$comment = $event->getComment();
		$user    = $comment->getAuthor();
		
		$this->logger->info(
			sprintf(
				'User "%s" - ID "%d" created a comment - "%d", for post "%d".',
				$user->getUsername(),
				$user->getId(),
				$comment->getId(),
				$comment->getPost()->getId()
			)
		);
	}
}