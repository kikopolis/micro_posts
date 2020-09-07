<?php

declare(strict_types = 1);

namespace App\EventSubscriber\User;

use App\Event\User\DeleteEvent;
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
	public function delete(DeleteEvent $event)
	{
		$this->entityManager->remove($event->getUser());
	}
	
	/**
	 * @param  DeleteEvent  $event
	 */
	public function log(DeleteEvent $event)
	{
		$user  = $event->getUser();
		$admin = $event->getDeletedBy();
		
		$this->logger->info(
			sprintf(
				'User "%s", "%d" has been deleted by admin "%s", "%d"',
				$user->getUsername(),
				$user->getId(),
				$admin->getUsername(),
				$admin->getId()
			)
		);
	}
}