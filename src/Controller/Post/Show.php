<?php

declare(strict_types = 1);

namespace App\Controller\Post;

use App\Controller\AbstractController;
use App\Entity\Post;
use App\Event\Post\ViewEvent;
use App\Repository\PostRepository;
use App\Security\Voter\Contracts\VotablesContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Show
 * @package App\Controller\Post
 */
class Show extends AbstractController
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
	 * @var PostRepository
	 */
	private PostRepository $postRepository;
	
	/**
	 * Show constructor.
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
	 *     "/posts/{id}/show",
	 *     name="post.show",
	 *     methods={"GET"},
	 *     requirements={"id"="\d+"}
	 * )
	 * @param  Post  $post
	 * @return Response
	 */
	public function __invoke(Post $post): Response
	{
		if (! $this->isGranted(
			VotablesContract::VIEW, $post
		)) {
			
			throw $this->createNotFoundException('No post found. Try a different one.');
		}
		
		$this->eventDispatcher->dispatch(
			new ViewEvent($post)
		);
		
		$this->entityManager->flush();
		
		return $this->render(
			'post/show.html.twig',
			[
				'post' => $post,
			]
		);
	}
}