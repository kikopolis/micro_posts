<?php

declare(strict_types = 1);

namespace App\Controller\Notification;

use App\Controller\AbstractController;
use App\Entity\Notification;
use App\Event\Notification\MassReadEvent;
use App\Repository\NotificationRepository;
use App\Security\Voter\Contracts\VotablesContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class ReadAll
 * @package App\Controller\Notification
 */
class ReadAll extends AbstractController
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
	 * @var NotificationRepository
	 */
	private NotificationRepository $notificationRepository;
	
	/**
	 * ReadAll constructor.
	 * @param  EntityManagerInterface    $entityManager
	 * @param  EventDispatcherInterface  $eventDispatcher
	 * @param  NotificationRepository    $notificationRepository
	 */
	public function __construct(
		EntityManagerInterface $entityManager,
		EventDispatcherInterface $eventDispatcher,
		NotificationRepository $notificationRepository
	)
	{
		$this->entityManager          = $entityManager;
		$this->eventDispatcher        = $eventDispatcher;
		$this->notificationRepository = $notificationRepository;
	}
	
	/**
	 * @Route(
	 *     "/notifications/read/all",
	 *     name="mass.notifications.read",
	 *     methods={"GET"}
	 * )
	 * @return Response
	 */
	public function __invoke(): Response
	{
		$user = $this->getUser();
		
		if (! $user) {
			
			return $this->json(
				null,
				Response::HTTP_UNAUTHORIZED
			);
		}
		
		$notifications = $this->notificationRepository->findBy(
			[
				'author' => $user,
				'seen'   => false,
			]
		);
		
		foreach ($notifications as $notification) {
			
			$this->denyAccessUnlessGranted(
				VotablesContract::MARK_READ,
				$notification
			);
		}
		
		$this->eventDispatcher->dispatch(
			new MassReadEvent($notifications)
		);
		
		$this->entityManager->flush();
		
		return $this->json(
			null,
			Response::HTTP_NO_CONTENT
		);
	}
}