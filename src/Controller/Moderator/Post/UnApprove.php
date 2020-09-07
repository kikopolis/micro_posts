<?php

declare(strict_types = 1);

namespace App\Controller\Moderator\Post;

use App\Controller\AbstractController;
use App\Controller\Concerns\DisableDoctrineFiltersConcern;
use App\Event\Post\UnApproveEvent;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class UnApprove
 * @package App\Controller\Moderator\Post
 */
class UnApprove extends AbstractController
{
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
	 * @var PostRepository
	 */
	private PostRepository $postRepository;
	
	/**
	 * Approve constructor.
	 * @param  EntityManagerInterface    $entityManager
	 * @param  EventDispatcherInterface  $eventDispatcher
	 * @param  PostRepository            $postRepository
	 */
	public function __construct(
		EntityManagerInterface $entityManager,
		EventDispatcherInterface $eventDispatcher,
		PostRepository $postRepository
	)
	{
		$this->entityManager   = $entityManager;
		$this->eventDispatcher = $eventDispatcher;
		$this->postRepository  = $postRepository;
	}
	
	/**
	 * @Route(
	 *     "/moderator/posts/{id}/un-approve",
	 *     name="mod.post.un.approve",
	 *     methods={"POST"},
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
		
		$post = $this->postRepository->find($id);
		
		if (! $post) {
			
			return $this->json(null, Response::HTTP_NOT_FOUND);
		}
		
		$this->eventDispatcher->dispatch(
			new UnApproveEvent(
				$this->getUser(),
				$post
			)
		);
		
		$this->entityManager->flush();
		
		return $this->json(
			null,
			Response::HTTP_NO_CONTENT
		);
	}
}