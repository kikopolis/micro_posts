<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Post;

use App\Event\Post\UnApproveEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UnApproveSubscriber
 * @package App\EventSubscriber\Post
 */
class UnApproveSubscriber implements EventSubscriberInterface
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
			UnApproveEvent::class => [
				['unApprove', 998],
				['log', 998],
			],
		];
	}
	
	/**
	 * @param  UnApproveEvent  $event
	 */
	public function unApprove(UnApproveEvent $event): void
	{
		$post = $event->getPost();
		
		$post->unApprove();
		$post->setApprovedBy(null);
		$post->setUnApprovedBy($event->getUnApprovedBy());
	}
	
	/**
	 * @param  UnApproveEvent  $event
	 */
	public function log(UnApproveEvent $event): void
	{
		$user = $event->getUnApprovedBy();
		
		$this->logger->info(
			sprintf(
				'User "%s", ID - "%d" has un-approved the post "%d"',
				$user->getUsername(),
				$user->getId(),
				$event->getPost()->getId()
			)
		);
	}
}