<?php

declare(strict_types = 1);

namespace App\Controller\Post;

use App\Controller\AbstractController;
use App\Controller\Concerns\ExpectsJsonConcern;
use App\Entity\User;
use App\Event\Post\ReportEvent;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Service\Contracts\FlashContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Report
 * @package App\Controller\Post
 */
class Report extends AbstractController
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
	 * @var UserRepository
	 */
	private UserRepository $userRepository;
	
	/**
	 * @var PostRepository
	 */
	private PostRepository $postRepository;
	
	/**
	 * Report constructor.
	 * @param  EntityManagerInterface    $entityManager
	 * @param  EventDispatcherInterface  $eventDispatcher
	 * @param  UserRepository            $userRepository
	 * @param  PostRepository            $postRepository
	 */
	public function __construct(
		EntityManagerInterface $entityManager,
		EventDispatcherInterface $eventDispatcher,
		UserRepository $userRepository,
		PostRepository $postRepository
	)
	{
		$this->entityManager   = $entityManager;
		$this->eventDispatcher = $eventDispatcher;
		$this->userRepository  = $userRepository;
		$this->postRepository  = $postRepository;
	}
	
	/**
	 * @Route(
	 *     "/posts/{id}/report",
	 *     name="post.report",
	 *     methods={"GET", "POST"},
	 *     requirements={"id"="\d+"}
	 * )
	 * @param  int      $id
	 * @param  Request  $request
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
				'You must login for this action.'
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
		
		$mods = $this->userRepository->findBy(['roles' => [User::ROLE_MODERATOR]]);
		
		$this->eventDispatcher->dispatch(
			new ReportEvent(
				$mods,
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
			'Post reported as inappropriate. A mod will review it asap.'
		);
		
		return $this->redirectToRoute(
			'post.show',
			[
				'id' => $post->getId(),
			]
		);
	}
}