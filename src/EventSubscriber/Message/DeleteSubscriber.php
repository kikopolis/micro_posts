<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Message;

use App\Event\Message\DeleteEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DeleteSubscriber
 * @package App\EventSubscriber\Message
 */
class DeleteSubscriber implements EventSubscriberInterface
{
	/**
	 * @var EntityManagerInterface
	 */
	private EntityManagerInterface $entityManager;
	
	/**
	 * DeleteSubscriber constructor.
	 * @param  EntityManagerInterface  $entityManager
	 */
	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}
	
	/**
	 * @return array[]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			DeleteEvent::class => ['delete', 999],
		];
	}
	
	/**
	 * @param  DeleteEvent  $event
	 */
	public function delete(DeleteEvent $event): void
	{
		$this->entityManager->remove($event->getMessage());
	}
}