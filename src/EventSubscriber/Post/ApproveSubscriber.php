<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Post;

use App\Event\Post\ApproveEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ApproveSubscriber
 * @package App\EventSubscriber\Post
 */
class ApproveSubscriber implements EventSubscriberInterface
{
	/**
	 * @var LoggerInterface
	 */
	private LoggerInterface $logger;
	
	/**
	 * ApproveSubscriber constructor.
	 * @param  LoggerInterface  $logger
	 */
	public function __construct(
		LoggerInterface $logger
	)
	{
		$this->logger = $logger;
	}
	
	/**
	 * @return \array[][]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			ApproveEvent::class => [
				['approve', 998],
				['log', 998],
			],
		];
	}
	
	/**
	 * @param  ApproveEvent  $event
	 */
	public function approve(ApproveEvent $event): void
	{
		$post = $event->getPost();
		
		$post->approve();
		$post->setApprovedBy($event->getApprovedBy());
	}
	
	/**
	 * @param  ApproveEvent  $event
	 */
	public function log(ApproveEvent $event): void
	{
		$user = $event->getApprovedBy();
		
		$this->logger->info(
			sprintf(
				'User "%s", ID - "%d" has approved the post "%d"',
				$user->getUsername(),
				$user->getId(),
				$event->getPost()->getId()
			)
		);
	}
}