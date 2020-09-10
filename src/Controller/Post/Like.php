<?php

declare(strict_types = 1);

namespace App\Controller\Post;

use App\Controller\AbstractController;
use App\Controller\Concerns\ExpectsJsonConcern;
use App\Entity\User;
use App\Event\Post\LikeEvent;
use App\Repository\PostRepository;
use App\Service\Contracts\FlashContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Like
 * @package App\Controller\Post
 */
class Like extends AbstractController
{
	use ExpectsJsonConcern;
	
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
	 * Like constructor.
	 * @param   EntityManagerInterface     $entityManager
	 * @param   EventDispatcherInterface   $eventDispatcher
	 * @param   PostRepository             $postRepository
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
	 *     "/posts/{id}/like",
	 *     name="post.like",
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
		
		$this->eventDispatcher->dispatch(
			new LikeEvent(
				$this->getUser(),
				$post
			)
		);
		
		$this->entityManager->flush();
		
		if ($this->expectsJson($request)) {
			
			return $this->json(
				[
					'likeCount' => $post->getLikedBy()->count(),
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
				'id' => $post->getId(),
			]
		);
	}
}