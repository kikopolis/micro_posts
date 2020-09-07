<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Post;

use App\Event\Post\DeleteEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DeleteSubscriber
 * @package App\EventSubscriber\Post
 */
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
		$this->entityManager->remove($event->getPost());
	}
	
	/**
	 * @param  DeleteEvent  $event
	 */
	public function log(DeleteEvent $event): void
	{
		$user = $event->getDeleter();
		
		$this->logger->info(
			sprintf(
				'User "%s", ID - "%d" has deleted the post "%d"',
				$user->getUsername(),
				$user->getId(),
				$event->getPost()->getId()
			)
		);
	}
}