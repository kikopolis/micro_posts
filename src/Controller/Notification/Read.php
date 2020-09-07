<?php

declare(strict_types = 1);

namespace App\Controller\Notification;

use App\Controller\AbstractController;
use App\Entity\Notification;
use App\Event\Notification\ReadEvent;
use App\Security\Voter\Contracts\VotablesContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class Read
 * @package App\Controller\Notification
 */
class Read extends AbstractController
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
	 * Read constructor.
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
	 *     "/notifications/{id}/read",
	 *     name="notifcation.read",
	 *     methods={"GET"},
	 *     requirements={"id"="\d+"}
	 * )
	 * @param  Notification  $notification
	 * @return Response
	 */
	public function __invoke(Notification $notification): Response
	{
		$user = $this->getUser();
		
		if (! $user) {
			
			return $this->json(
				null,
				Response::HTTP_UNAUTHORIZED
			);
		}
		
		$this->denyAccessUnlessGranted(
			VotablesContract::MARK_READ,
			$notification
		);
		
		$this->eventDispatcher->dispatch(
			new ReadEvent($notification)
		);
		
		$this->entityManager->flush();
		
		return $this->json(
			null,
			Response::HTTP_NO_CONTENT
		);
	}
}