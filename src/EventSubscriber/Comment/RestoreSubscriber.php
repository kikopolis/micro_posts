<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Comment;

use App\Event\Comment\RestoreEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class RestoreSubscriber
 * @package App\EventSubscriber\Comment
 */
class RestoreSubscriber implements EventSubscriberInterface
{
	/**
	 * @var LoggerInterface
	 */
	private LoggerInterface $logger;
	
	/**
	 * RestoreSubscriber constructor.
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
			RestoreEvent::class => [
				['restore', 999],
				['log', 998],
			],
		];
	}
	
	/**
	 * @param  RestoreEvent  $event
	 */
	public function restore(RestoreEvent $event): void
	{
		$comment = $event->getComment();
		
		$comment->restore();
		$comment->setRestoredBy($event->getRestoredBy());
	}
	
	/**
	 * @param  RestoreEvent  $event
	 */
	public function log(RestoreEvent $event): void
	{
		$user = $event->getRestoredBy();
		
		$this->logger->info(
			sprintf(
				'User "%s", ID - "%d" has restored the comment "%d" from trash',
				$user->getUsername(),
				$user->getId(),
				$event->getComment()->getId()
			)
		);
	}
}