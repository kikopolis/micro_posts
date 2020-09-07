<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Post;

use App\Event\Post\UnReportEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UnReportSubscriber
 * @package App\EventSubscriber\Post
 */
class UnReportSubscriber implements EventSubscriberInterface
{
	/**
	 * @var LoggerInterface
	 */
	private LoggerInterface $logger;
	
	/**
	 * UnReportSubscriber constructor.
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
			UnReportEvent::class => [
				['unReport', 999],
				['log', 998],
			],
		];
	}
	
	/**
	 * @param  UnReportEvent  $event
	 */
	public function unReport(UnReportEvent $event): void
	{
		$event->getPost()->unReport();
	}
	
	/**
	 * @param  UnReportEvent  $event
	 */
	public function log(UnReportEvent $event): void
	{
		$user = $event->getMod();
		
		$this->logger->info(
			sprintf(
				'Moderator "%s", "%d" has cleared the post "%d" of all reports.',
				$user->getUsername(),
				$user->getId(),
				$event->getPost()->getId()
			)
		);
	}
}