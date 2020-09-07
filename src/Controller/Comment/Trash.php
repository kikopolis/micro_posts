<?php

declare(strict_types = 1);

namespace App\Controller\Comment;

use App\Controller\AbstractController;
use App\Entity\Comment;
use App\Event\Comment\TrashEvent;
use App\Security\Voter\Contracts\VotablesContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Trash
 * @package App\Controller\Comment
 */
class Trash extends AbstractController
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
	 * Trash constructor.
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
	 *     "/comments/{id}/trash",
	 *     name="comment.trash",
	 *     methods={"DELETE"},
	 *     requirements={"id"="\d+"}
	 * )
	 * @param  Comment  $comment
	 * @return Response
	 */
	public function __invoke(Comment $comment): Response
	{
		if (
			! $this->isGranted('ROLE_USER')
			|| ! $this->isGranted(VotablesContract::TRASH, $comment)
		) {
			
			return $this->json(
				null,
				Response::HTTP_UNAUTHORIZED
			);
		}
		
		$this->eventDispatcher->dispatch(
			new TrashEvent(
				$this->getUser(),
				$comment
			)
		);
		
		$this->entityManager->flush();
		
		return $this->json(
			null,
			Response::HTTP_NO_CONTENT
		);
	}
}