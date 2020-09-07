<?php

declare(strict_types = 1);

namespace App\Controller\Complaint;

use App\Controller\AbstractController;
use App\Entity\Complaint;
use App\Event\Complaint\DeleteEvent;
use App\Security\Voter\Contracts\VotablesContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class Delete
 * @package App\Controller\Complaint
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
	 *     "/complaints/{id}/delete",
	 *     name="complaint.delete",
	 *     methods={"DELETE"},
	 *     requirements={"id"="\d+"}
	 * )
	 * @param  Complaint  $complaint
	 * @return Response
	 */
	public function __invoke(Complaint $complaint): Response
	{
		if (! $this->isGranted(VotablesContract::DELETE, $complaint)) {
			
			return $this->json(
				null,
				Response::HTTP_UNAUTHORIZED
			);
		}
		
		$this->eventDispatcher->dispatch(
			new DeleteEvent($this->getUser(), $complaint)
		);
		
		$this->entityManager->flush();
		
		return $this->json(
			null,
			Response::HTTP_NO_CONTENT
		);
	}
}