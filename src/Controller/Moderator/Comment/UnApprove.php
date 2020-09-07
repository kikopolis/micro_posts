<?php

declare(strict_types = 1);

namespace App\Controller\Moderator\Comment;

use App\Controller\AbstractController;
use App\Controller\Concerns\DisableDoctrineFiltersConcern;
use App\Event\Comment\UnApproveEvent;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class UnApprove
 * @package App\Controller\Moderator\Comment
 */
class UnApprove extends AbstractController
{use DisableDoctrineFiltersConcern;
	/**
	 * @var EntityManagerInterface
	 */
	private EntityManagerInterface $entityManager;
	
	/**
	 * @var EventDispatcherInterface
	 */
	private EventDispatcherInterface $eventDispatcher;
	
	/**
	 * @var CommentRepository
	 */
	private CommentRepository $commentRepository;
	
	/**
	 * Approve constructor.
	 * @param  EntityManagerInterface    $entityManager
	 * @param  EventDispatcherInterface  $eventDispatcher
	 * @param  CommentRepository         $commentRepository
	 */
	public function __construct(
		EntityManagerInterface $entityManager,
		EventDispatcherInterface $eventDispatcher,
	CommentRepository $commentRepository
	)
	{
		$this->entityManager   = $entityManager;
		$this->eventDispatcher = $eventDispatcher;
		$this->commentRepository = $commentRepository;
	}
	
	/**
	 * @Route(
	 *     "/moderator/comments/{id}/un-approve",
	 *     methods={"POST"},
	 *     name="mod.comment.un.approve",
	 *     requirements={"id"="\d+"}
	 * )
	 * @param  int  $id
	 * @return Response
	 */
	public function __invoke(int $id): Response
	{
		if (
			! $this->isGranted('ROLE_MODERATOR')
			|| ! $this->isGranted('IS_AUTHENTICATED_FULLY')
		) {
			
			return $this->json(
				[
					'message' => 'You must be fully logged in for moderator functions.
			Please log in again.',
				],
				Response::HTTP_UNAUTHORIZED
			);
		}
		
		$this->disableMany(['approvable_filter', 'publishable_filter', 'trashable_filter']);
		
		$comment = $this->commentRepository->find($id);
		
		if (! $comment) {
			
			return $this->json(null, Response::HTTP_NOT_FOUND);
		}
		
		$this->eventDispatcher->dispatch(
			new UnApproveEvent(
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