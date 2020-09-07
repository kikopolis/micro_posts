<?php

declare(strict_types = 1);

namespace App\EventSubscriber\User;

use App\Event\User\TrashEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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
		$user      = $event->getUser();
		$trashedBy = $event->getTrashedBy();
		
		$user->trash();
		
		if ($trashedBy) {
			
			$user->setTrashedBy($trashedBy);
		}
	}
	
	/**
	 * @param  TrashEvent  $event
	 */
	public function log(TrashEvent $event): void
	{
		$user      = $event->getUser();
		$trashedBy = $event->getTrashedBy();
		
		if (! $trashedBy) {
			$this->logger->info(
				sprintf(
					'User "%s", "%d" has sent their account to trash.',
					$user->getUsername(),
					$user->getId()
				)
			);
		} else {
			$this->logger->info(
				sprintf(
					'User "%s", "%d" has been trashed by "%s", "%d" their account to trash.',
					$user->getUsername(),
					$user->getId(),
					$trashedBy->getUsername(),
					$trashedBy->getId()
				)
			);
		}
	}
}