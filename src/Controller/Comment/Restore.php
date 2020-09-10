<?php

declare(strict_types = 1);

namespace App\Controller\Comment;

use App\Controller\AbstractController;
use App\Controller\Concerns\DisableDoctrineFiltersConcern;
use App\Controller\Concerns\ExpectsJsonConcern;
use App\Entity\User;
use App\Event\Comment\RestoreEvent;
use App\Repository\CommentRepository;
use App\Security\Voter\Contracts\VotablesContract;
use App\Service\Contracts\FlashContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Restore
 * @package App\Controller\Comment
 */
class Restore extends AbstractController
{
	use ExpectsJsonConcern;
	use DisableDoctrineFiltersConcern;
	
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
	 * Restore constructor.
	 * @param   EntityManagerInterface     $entityManager
	 * @param   EventDispatcherInterface   $eventDispatcher
	 * @param   CommentRepository          $commentRepository
	 */
	public function __construct(
		EntityManagerInterface $entityManager,
		EventDispatcherInterface $eventDispatcher,
		CommentRepository $commentRepository
	)
	{
		$this->entityManager     = $entityManager;
		$this->eventDispatcher   = $eventDispatcher;
		$this->commentRepository = $commentRepository;
	}
	
	/**
	 * @Route(
	 *     "/comments/{id}/restore",
	 *     name="comment.restore",
	 *     methods={"GET", "POST"},
	 *     requirements={"id"="\d+"}
	 * )
	 * @param   int       $id
	 * @param   Request   $request
	 * @return Response
	 */
	public function __invoke(int $id, Request $request): Response
	{
		$this->disableAllDoctrineFilters();
		
		$comment = $this->commentRepository->find($id);
		
		if (! $comment) {
			
			if ($this->expectsJson($request)) {
				
				return $this->json(
					null,
					Response::HTTP_NOT_FOUND
				);
			}
			
			throw $this->createNotFoundException('Comment not found.');
		}
		
		if (! $this->isGranted(User::ROLE_USER)) {
			
			if ($this->expectsJson($request)) {
				
				return $this->json(
					null,
					Response::HTTP_BAD_REQUEST
				);
			}
			
			$this->addFlash(
				FlashContract::WARNING,
				'Please login for this action.'
			);
			
			$this->redirectToRoute('login');
		}
		
		if (! $this->isGranted(VotablesContract::RESTORE, $comment)) {
			
			if ($this->expectsJson($request)) {
				
				return $this->json(
					null,
					Response::HTTP_UNAUTHORIZED
				);
			}
			
			$this->addFlash(
				FlashContract::WARNING,
				'Action not authorized.'
			);
			
			return $this->redirectToRoute(
				'post.show',
				[
					'id' => $comment->getPost()->getId(),
				]
			);
		}
		
		$this->eventDispatcher->dispatch(
			new RestoreEvent(
				$this->getUser()
				, $comment
			)
		);
		
		$this->entityManager->flush();
		
		if ($this->expectsJson($request)) {
			
			return $this->json(
				null,
				Response::HTTP_NO_CONTENT
			);
		}
		
		$this->addFlash(
			FlashContract::SUCCESS,
			'Comment restored successfully.'
		);
		
		return $this->redirectToRoute(
			'comment.preview',
			[
				'id' => $comment->getId(),
			]
		);
	}
}