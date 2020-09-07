<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Conversation;

use App\Event\Conversation\DeleteEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DeleteSubscriber
 * @package App\EventSubscriber\Conversation
 */
class DeleteSubscriber implements EventSubscriberInterface
{
	/**
	 * @var EntityManagerInterface
	 */
	private EntityManagerInterface $entityManager;
	
	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}
	
	public static function getSubscribedEvents(): array
	{
		return [
			DeleteEvent::class => ['delete', 999],
		];
	}
	
	public function delete(DeleteEvent $event): void
	{
		$this->entityManager->remove($event->getConversation());
	}
}