<?php

declare(strict_types = 1);

namespace App\Controller\Notification;

use App\Controller\AbstractController;
use App\Entity\Notification;
use App\Entity\User;
use App\Event\Notification\DeleteEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class Delete
 * @package App\Controller\Notification
 */
class Delete extends AbstractController
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
	 * Delete constructor.
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
	 * @Route(
	 *     "/notifications/{id}/delete",
	 *     name="notification.delete",
	 *     methods={"DELETE"},
	 *     requirements={"id"="\d+"}
	 * )
	 * @param  Notification  $notification
	 * @return Response
	 */
	public function __invoke(Notification $notification): Response
	{
		/** @var User $user */
		$user = $this->getUser();
		
		if (! $user || $user->getId() !== $notification->getOwner()->getId()) {
			
			return $this->json(
				null,
				Response::HTTP_UNAUTHORIZED
			);
		}
		
		$this->eventDispatcher->dispatch(
			new DeleteEvent($notification)
		);
		
		$this->entityManager->flush();
		
		return $this->json(
			null,
			Response::HTTP_NO_CONTENT
		);
	}
}