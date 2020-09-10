<?php

declare(strict_types = 1);

namespace App\Controller\Comment;

use App\Controller\AbstractController;
use App\Controller\Concerns\ExpectsJsonConcern;
use App\Entity\User;
use App\Event\Comment\LikeEvent;
use App\Repository\CommentRepository;
use App\Service\Contracts\FlashContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Like
 * @package App\Controller\Comment
 */
class Like extends AbstractController
{use ExpectsJsonConcern;
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
	 * Like constructor.
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
	 *     "/comments/{id}/like",
	 *     name="comment.like",
	 *     methods={"GET", "POST"},
	 *     requirements={"id"="\d+"}
	 * )
	 * @param   int       $id
	 * @param   Request   $request
	 * @return Response
	 */
	public function __invoke(int $id, Request $request): Response
	{
		if (! $this->isGranted(User::ROLE_USER)) {
			
			if ($this->expectsJson($request)) {
				
				return $this->json(
					null,
					Response::HTTP_UNAUTHORIZED
				);
			}
			
			$this->addFlash(
				FlashContract::WARNING,
				'You must be logged in for this action.'
			);
			
			return $this->redirectToRoute('login');
		}
		
		$comment = $this->commentRepository->find($id);
		
		$this->eventDispatcher->dispatch(
			new LikeEvent(
				$this->getUser(),
				$comment
			)
		);
		
		$this->entityManager->flush();
		
		if ($this->expectsJson($request)) {
			
			return $this->json(
				[
					'likeCount' => $comment->getLikedBy()->count(),
				]
			);
		}
		
		$this->addFlash(
			FlashContract::SUCCESS,
			'Like added successfully!'
		);
		
		return $this->redirectToRoute(
			'post.show',
			[
				'id' => $comment->getPost()->getId(),
			]
		);
	}
}