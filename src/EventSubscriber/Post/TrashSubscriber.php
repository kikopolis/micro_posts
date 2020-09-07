<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Post;

use App\Event\Post\TrashEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class TrashSubscriber
 * @package App\EventSubscriber\Post
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
	 * @return array|\array[][]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			TrashEvent::class => [
				['trash', 999],
				['log', 998],
			],
		];
	}
	
	/**
	 * @param  TrashEvent  $event
	 */
	public function trash(TrashEvent $event): void
	{
		$post = $event->getPost();
		
		$post->unPublish();
		$post->trash();
		$post->setTrashedBy($event->getTrashedBy());
	}
	
	/**
	 * @param  TrashEvent  $event
	 */
	public function log(TrashEvent $event): void
	{
		$user = $event->getTrashedBy();
		
		$this->logger->info(
			sprintf(
				'User "%s", ID - "%d" has trashed the post "%d"',
				$user->getUsername(),
				$user->getId(),
				$event->getPost()->getId()
			)
		);
	}
}