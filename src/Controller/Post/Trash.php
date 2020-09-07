<?php

declare(strict_types = 1);

namespace App\Controller\Post;

use App\Controller\AbstractController;
use App\Controller\Concerns\ExpectsJsonConcern;
use App\Entity\User;
use App\Event\Post\TrashEvent;
use App\Repository\PostRepository;
use App\Security\Voter\Contracts\VotablesContract;
use App\Service\Contracts\FlashContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Trash
 * @package App\Controller\Post
 */
class Trash extends AbstractController
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
	 * Trash constructor.
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
	 *     "/posts/{id}/trash",
	 *     name="post.trash",
	 *     methods={"GET", "DELETE"},
	 *     requirements={"id"="\d+"}
	 * )
	 * @param  int      $id
	 * @param  Request  $request
	 * @return Response
	 */
	public function __invoke(int $id, Request $request): Response
	{
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
		
		if (! $this->isGranted(VotablesContract::TRASH, $post)) {
			
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
			new TrashEvent(
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
		
		$this->addFlash(
			FlashContract::SUCCESS,
			'Post has been sent to trash. Only You and moderators can see it now.'
		);
		
		return $this->redirectToRoute(
			'post.preview',
			[
				'id' => $post->getId(),
			]
		);
	}
}