<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Post;

use App\Event\Post\UnPublishEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UnPublishSubscriber
 * @package App\EventSubscriber\Post
 */
class UnPublishSubscriber implements EventSubscriberInterface
{
	/**
	 * @var LoggerInterface
	 */
	private LoggerInterface $logger;
	
	/**
	 * UnPublishSubscriber constructor.
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
			UnPublishEvent::class => [
				['unPublish', 999],
				['log', 998],
			],
		];
	}
	
	/**
	 * @param  UnPublishEvent  $event
	 */
	public function unPublish(UnPublishEvent $event): void
	{
		$post = $event->getPost();
		
		$post->unPublish();
		$post->setUnPublishedBy($event->getUnPublishedBy());
	}
	
	/**
	 * @param  UnPublishEvent  $event
	 */
	public function log(UnPublishEvent $event): void
	{
		$user = $event->getUnPublishedBy();
		
		$this->logger->info(
			sprintf(
				'User "%s", ID - "%d" has un-published the post "%d"',
				$user->getUsername(),
				$user->getId(),
				$event->getPost()->getId()
			)
		);
	}
}