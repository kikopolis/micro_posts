<?php

declare(strict_types = 1);

namespace App\Controller\Comment;

use App\Controller\AbstractController;
use App\Entity\Comment;
use App\Entity\User;
use App\Event\Comment\LikeEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Like
 * @package App\Controller\Comment
 */
class Like extends AbstractController
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
	 * Like constructor.
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
	 *     "/comments/{id}/like",
	 *     name="comment.like",
	 *     methods={"POST"},
	 *     requirements={"id"="\d+"}
	 * )
	 * @param  Comment  $comment
	 * @return Response
	 */
	public function __invoke(Comment $comment): Response
	{
		if (! $this->isGranted('ROLE_USER')) {
			
			return $this->json(
				null,
				Response::HTTP_UNAUTHORIZED
			);
		}
		
		/** @var User $user */
		$user = $this->getUser();
		
		// User cannot like a comment they have already liked or if they are the author
		if (
			$comment->getLikedBy()->contains($user)
			|| $comment->getAuthor()->getId() === $user->getId()
		) {
			
			return $this->json(
				[
					'likeCount' => $comment->getLikedBy()->count(),
				]
			);
		}
		
		$this->eventDispatcher->dispatch(
			new LikeEvent(
				$user,
				$comment
			)
		);
		
		$this->entityManager->flush();
		
		return $this->json(
			[
				'likeCount' => $comment->getLikeCount(),
			]
		);
	}
}