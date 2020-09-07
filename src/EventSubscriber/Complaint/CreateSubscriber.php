<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Complaint;

use App\Entity\Complaint;
use App\Entity\Notification\ComplaintCreatedNotification;
use App\Entity\User;
use App\Event\Complaint\CreateEvent;
use App\Event\TimeStampableCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class CreateSubscriber
 * @package App\EventSubscriber\Complaint
 */
class CreateSubscriber implements EventSubscriberInterface
{
	/**
	 * @var EntityManagerInterface
	 */
	private EntityManagerInterface $entityManager;
	
	/**
	 * @var EventDispatcherInterface
	 */
	private EventDispatcherInterface $eventDispatcher;
	
	/**
	 * CreateSubscriber constructor.
	 * @param  EntityManagerInterface    $entityManager
	 * @param  EventDispatcherInterface  $eventDispatcher
	 */
	public function __construct(
		EntityManagerInterface $entityManager,
		EventDispatcherInterface $eventDispatcher
	)
	{
		$this->entityManager   = $entityManager;
		$this->eventDispatcher = $eventDispatcher;
	}
	
	/**
	 * @return array|\array[][]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			CreateEvent::class => [
				['notify', 999],
			],
		];
	}
	
	/**
	 * @param  CreateEvent  $event
	 */
	public function notify(CreateEvent $event): void
	{
		$complaint    = $event->getComplaint();
		$complainedBy = $complaint->getAuthor();
		
		foreach ($event->getAdmins() as $admin) {
			
			$this->note($admin, $complaint, $complainedBy);
		}
	}
	
	/**
	 * @param  User       $admin
	 * @param  Complaint  $complaint
	 * @param  User       $complainedBy
	 */
	private function note(User $admin, Complaint $complaint, User $complainedBy): void
	{
		$note = new ComplaintCreatedNotification(
			$admin,
			$complaint,
			$complainedBy
		);
		
		$this->eventDispatcher->dispatch(
			new TimeStampableCreatedEvent($note)
		);
		
		$this->entityManager->persist($note);
	}
}