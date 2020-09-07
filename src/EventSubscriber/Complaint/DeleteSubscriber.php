<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Complaint;

use App\Event\Complaint\DeleteEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DeleteSubscriber implements EventSubscriberInterface
{
	/**
	 * @var EntityManagerInterface
	 */
	private EntityManagerInterface $entityManager;
	
	/**
	 * @var LoggerInterface
	 */
	private LoggerInterface $logger;
	
	/**
	 * DeleteSubscriber constructor.
	 * @param  EntityManagerInterface  $entityManager
	 * @param  LoggerInterface         $logger
	 */
	public function __construct(
		EntityManagerInterface $entityManager,
		LoggerInterface $logger
	)
	{
		$this->entityManager = $entityManager;
		$this->logger        = $logger;
	}
	
	/**
	 * @return array|\array[][]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			DeleteEvent::class => [
				['delete', 999],
				['log', 998],
			],
		];
	}
	
	/**
	 * @param  DeleteEvent  $event
	 */
	public function delete(DeleteEvent $event): void
	{
		$this->entityManager->remove($event->getComplaint());
	}
	
	/**
	 * @param  DeleteEvent  $event
	 */
	public function log(DeleteEvent $event): void
	{
		$user = $event->getDeletedBy();
		
		$this->logger->info(
			sprintf(
				'User "%s", "%d" has deleted the complaint "%d"',
				$user->getUsername(),
				$user->getId(),
				$event->getComplaint()->getId()
			)
		);
	}
}