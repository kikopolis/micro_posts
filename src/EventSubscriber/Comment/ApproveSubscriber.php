<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Comment;

use App\Event\Comment\ApproveEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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
			ApproveEvent::class => [
				['approve', 999],
				['log', 998],
			],
		];
	}
	
	/**
	 * @param  ApproveEvent  $event
	 */
	public function approve(ApproveEvent $event)
	{
		$event->getComment()
		      ->approve()
		      ->setApprovedBy(
			      $event->getApprovedBy()
		      )
		;
	}
	
	/**
	 * @param  ApproveEvent  $event
	 */
	public function log(ApproveEvent $event)
	{
		$user = $event->getApprovedBy();
		
		$this->logger->info(
			sprintf(
				'User "%s", ID - "%d" has approved the comment "%d"',
				$user->getUsername(),
				$user->getId(),
				$event->getComment()->getId()
			)
		);
	}
}