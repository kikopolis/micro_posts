<?php

declare(strict_types = 1);

namespace App\Controller\Post;

use App\Controller\AbstractController;
use App\Controller\Concerns\DisableDoctrineFiltersConcern;
use App\Controller\Concerns\ExpectsJsonConcern;
use App\Entity\User;
use App\Event\Post\RestoreEvent;
use App\Repository\PostRepository;
use App\Security\Voter\Contracts\VotablesContract;
use App\Service\Contracts\FlashContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Restore
 * @package App\Controller\Post
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
	 * @var PostRepository
	 */
	private PostRepository $postRepository;
	
	/**
	 * Restore constructor.
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
	 *     "/posts/{id}/restore",
	 *     name="post.restore",
	 *     methods={"GET", "POST"},
	 *     requirements={"id"="\d+"}
	 * )
	 * @param  int      $id
	 * @param  Request  $request
	 * @return Response
	 */
	public function __invoke(int $id, Request $request): Response
	{
		$this->disableAllDoctrineFilters();
		
		$post = $this->postRepository->find($id);
		
		if (! $post) {
			
			if ($this->expectsJson($request)) {
				
				return $this->json(
					null,
					Response::HTTP_NOT_FOUND
				);
			}
			
			throw $this->createNotFoundException('Post not found.');
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
		
		if (! $this->isGranted(VotablesContract::RESTORE, $post)) {
			
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
					'id' => $post->getId(),
				]
			);
		}
		
		$this->eventDispatcher->dispatch(
			new RestoreEvent(
				$this->getUser(),
				$post
			)
		);
		
		$this->entityManager->flush();
		
		if ($this->expectsJson($request)) {
			
			return $this->json(
				null,
				Response::HTTP_NO_CONTENT
			);
		}
		
		$this->addFlash(FlashContract::SUCCESS,
		'Post restored successfully.');
		
		return $this->redirectToRoute(
			'post.preview',
			[
				'id' => $post->getId(),
			]
		);
	}
}