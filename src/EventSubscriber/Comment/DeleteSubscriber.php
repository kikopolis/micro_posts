<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Comment;

use App\Event\Comment\DeleteEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DeleteSubscriber implements EventSubscriberInterface
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
	 * CommentDeletedSubscriber constructor.
	 * @param  EntityManagerInterface  $entityManager
	 * @param  LoggerInterface         $logger
	 */
	public function __construct(
		EntityManagerInterface $entityManager,
		LoggerInterface $logger
	)
	{
		$this->entityManager = $entityManager;
		$this->logger        = $logger;
	}
	
	/**
	 * @return array|array[]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			DeleteEvent::class => [
				['delete', 999],
				['log', 998],
			],
		];
	}
	
	/**
	 * @param  DeleteEvent  $event
	 */
	public function delete(DeleteEvent $event): void
	{
		$this->entityManager->remove($event->getComment());
	}
	
	/**
	 * @param  DeleteEvent  $event
	 */
	public function log(DeleteEvent $event): void
	{
		$comment = $event->getComment();
		$user    = $comment->getAuthor();
		
		$this->logger->info(
			sprintf(
				'User "%s" - ID "%d" deleted the comment "%d".',
				$user->getUsername(),
				$user->getId(),
				$comment->getId()
			)
		);
	}
}