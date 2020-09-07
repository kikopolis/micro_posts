<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Notification;

use App\Event\Notification\DeleteEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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
	 * @return array|array[]
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
		$this->entityManager->remove($event->getNotification());
	}
}