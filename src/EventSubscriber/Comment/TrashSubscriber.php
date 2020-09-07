<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Comment;

use App\Event\Comment\TrashEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class TrashSubscriber
 * @package App\EventSubscriber\Comment
 */
class TrashSubscriber implements EventSubscriberInterface
{
	/**
	 * @var LoggerInterface
	 */
	private LoggerInterface $logger;
	
	/**
	 * TrashSubscriber constructor.
	 * @param  LoggerInterface  $logger
	 */
	public function __construct(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}
	
	/**
	 * @return array|array[]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			TrashEvent::class => [
				['trash', 999],
				['log', 999],
			],
		];
	}
	
	/**
	 * @param  TrashEvent  $event
	 */
	public function trash(TrashEvent $event): void
	{
		$comment = $event->getComment();
		
		$comment->trash();
		$comment->setTrashedBy($event->getTrashedBy());
	}
	
	/**
	 * @param  TrashEvent  $event
	 */
	public function log(TrashEvent $event): void
	{
		$user = $event->getTrashedBy();
		
		$this->logger->info(
			sprintf(
				'User "%s", ID - "%d" has trashed the comment "%d"',
				$user->getUsername(),
				$user->getId(),
				$event->getComment()->getId()
			)
		);
	}
}