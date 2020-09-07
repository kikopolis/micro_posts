<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Message;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\Notification\NewMessageNotification;
use App\Entity\User;
use App\Event\Message\CreateEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class CreateSubscriber
 * @package App\EventSubscriber\Message
 */
class CreateSubscriber implements EventSubscriberInterface
{
	/**
	 * @var EntityManagerInterface
	 */
	private EntityManagerInterface $entityManager;
	
	/**
	 * CreateSubscriber constructor.
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
			CreateEvent::class => ['notify', 999],
		];
	}
	
	/**
	 * @param  CreateEvent  $event
	 */
	public function notify(CreateEvent $event): void
	{
		$conversation = $event->getConversation();
		$message      = $event->getMessage();
		
		foreach ($event->getParticipants() as $participant) {
			
			$this->note(
				$participant,
				$conversation,
				$message
			);
		}
	}
	
	/**
	 * @param  User          $user
	 * @param  Conversation  $conversation
	 * @param  Message       $message
	 */
	private function note(
		User $user,
		Conversation $conversation,
		Message $message
	): void
	{
		$note = new NewMessageNotification(
			$user,
			$message,
			$conversation,
			$message->getAuthor()
		);
		
		$this->entityManager->persist($note);
	}
}