<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Post;

use App\Event\Post\PublishEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class PublishSubscriber
 * @package App\EventSubscriber\Post
 */
class PublishSubscriber implements EventSubscriberInterface
{
	/**
	 * @var LoggerInterface
	 */
	private LoggerInterface $logger;
	
	/**
	 * PublishSubscriber constructor.
	 * @param  LoggerInterface  $logger
	 */
	public function __construct(
		LoggerInterface $logger
	)
	{
		$this->logger = $logger;
	}
	
	/**
	 * @return array|\array[][]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			PublishEvent::class => [
				['publish', 999],
				['log', 998],
			],
		];
	}
	
	/**
	 * @param  PublishEvent  $event
	 */
	public function publish(PublishEvent $event): void
	{
		$post = $event->getPost();
		
		$post->publish();
		$post->setPublishedBy($event->getPublishedBy());
	}
	
	/**
	 * @param  PublishEvent  $event
	 */
	public function log(PublishEvent $event): void
	{
		$user = $event->getPublishedBy();
		
		$this->logger->info(
			sprintf(
				'User "%s", ID - "%d" has published the post "%d"',
				$user->getUsername(),
				$user->getId(),
				$event->getPost()->getId()
			)
		);
	}
}