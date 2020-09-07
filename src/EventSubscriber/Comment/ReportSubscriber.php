<?php

declare(strict_types = 1);

namespace App\EventSubscriber\Comment;

use App\Entity\Notification\CommentReportedNotification;
use App\Event\Comment\ReportEvent;
use App\Event\TimeStampableCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class ReportSubscriber
 * @package App\EventSubscriber\Comment
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
	 * CommentReportedSubscriber constructor.
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
		$event->getComment()->report(
			$event->getReportedBy()
		)
		;
	}
	
	/**
	 * @param  ReportEvent  $event
	 */
	public function notify(ReportEvent $event): void
	{
		$comment = $event->getComment();
		
		foreach ($event->getMods() as $mod) {
			
			$note = new CommentReportedNotification(
				$mod,
				$comment,
				$comment->getAuthor()
			);
			
			$this->eventDispatcher->dispatch(
				new TimeStampableCreatedEvent($note)
			);
			
			$this->entityManager->persist($note);
		}
	}
}