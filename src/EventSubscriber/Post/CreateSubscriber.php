<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Post;

use App\Event\Post\CreateEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class CreateSubscriber
 * @package App\EventSubscriber\Post
 */
class CreateSubscriber implements EventSubscriberInterface
{
	/**
	 * @var LoggerInterface
	 */
	private LoggerInterface $logger;
	
	/**
	 * CreateSubscriber constructor.
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
			CreateEvent::class => [
				['publish', 999],
				['approve', 998],
			],
		];
	}
	
	/**
	 * @param  CreateEvent  $event
	 */
	public function publish(CreateEvent $event): void
	{
		$event->getPost()->publish();
	}
	
	/**
	 * @param  CreateEvent  $event
	 */
	public function approve(CreateEvent $event): void
	{
		$post = $event->getPost();
		
		// make sure post is unapproved
		$post->unApprove();
		//		$post->approve();
		//		$post->setApprovedBy($post->getAuthor());
	}
	
	/**
	 * @param  CreateEvent  $event
	 */
	public function log(CreateEvent $event): void
	{
		$post = $event->getPost();
		$user = $post->getAuthor();
		
		$this->logger->info(
			sprintf(
				'User "%s" - ID "%d" created the post "%d".',
				$user->getUsername(),
				$user->getId(),
				$post->getId()
			)
		);
	}
}