<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Post;

use App\Entity\Notification\PostReportedNotification;
use App\Entity\Post;
use App\Entity\User;
use App\Event\Post\ReportEvent;
use App\Event\TimeStampableCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class ReportSubscriber
 * @package App\EventSubscriber\Post
 */
class ReportSubscriber implements EventSubscriberInterface
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
	 * PostReportedEventSubscriber constructor.
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
	 * @return array|array[]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			ReportEvent::class => [
				['report', 999],
				['notify', 998],
			],
		];
	}
	
	/**
	 * @param  ReportEvent  $event
	 */
	public function report(ReportEvent $event): void
	{
		$event->getPost()->report(
			$event->getReportedBy()
		)
		;
	}
	
	/**
	 * @param  ReportEvent  $event
	 */
	public function notify(ReportEvent $event): void
	{
		$post       = $event->getPost();
		$reportedBy = $event->getReportedBy();
		
		foreach ($event->getMods() as $mod) {
			
			$this->note($mod, $post, $reportedBy);
		}
	}
	
	/**
	 * @param  User  $mod
	 * @param  Post  $post
	 * @param  User  $reportedBy
	 */
	protected function note(User $mod, Post $post, User $reportedBy): void
	{
		$note = new PostReportedNotification(
			$mod,
			$post,
			$reportedBy
		);
		
		$this->eventDispatcher->dispatch(
			new TimeStampableCreatedEvent($note)
		);
		
		$this->entityManager->persist($note);
	}
}