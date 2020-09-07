<?php

declare(strict_types = 1);

namespace App\Controller\Moderator\Post;

use App\Controller\AbstractController;
use App\Controller\Concerns\DisableDoctrineFiltersConcern;
use App\Controller\Concerns\HomeWithFlashConcern;
use App\Entity\User;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UnApproved
 * @package App\Controller\Moderator\Post
 */
class UnApproved extends AbstractController
{
	use HomeWithFlashConcern;
	use DisableDoctrineFiltersConcern;
	
	/**
	 * @var PostRepository
	 */
	private PostRepository $postRepository;
	
	/**
	 * @var PaginatorInterface
	 */
	private PaginatorInterface $paginator;
	
	/**
	 * @var EntityManagerInterface
	 */
	private EntityManagerInterface $entityManager;
	
	/**
	 * Reported constructor.
	 * @param  PostRepository          $postRepository
	 * @param  PaginatorInterface      $paginator
	 * @param  EntityManagerInterface  $entityManager
	 */
	public function __construct(
		PostRepository $postRepository,
		PaginatorInterface $paginator,
		EntityManagerInterface $entityManager
	)
	{
		$this->postRepository = $postRepository;
		$this->paginator      = $paginator;
		$this->entityManager  = $entityManager;
	}
	
	/**
	 * @Route(
	 *     "/moderator/posts/un-approved/{page}/{limit}",
	 *     name="mod.un.approved.posts",
	 *     methods={"GET"},
	 *     requirements={"page"="\d+", "limit"="\d+"},
	 *     defaults={"page"=1, "limit"=10}
	 * )
	 * @param  int  $page
	 * @param  int  $limit
	 * @return Response
	 */
	public function __invoke(int $page = 1, int $limit = 10): Response
	{
		$this->denyAccessUnlessGranted(User::ROLE_MODERATOR);
		
		if (! $this->isGranted('IS_AUTHENTICATED_FULLY')) {
			
			return $this->errorFlashHome(
				'You must be fully logged in for moderator functions.
			Please log in again.'
			);
		}
		
		$this->disableMany(['approvable_filter', 'publishable_filter', 'trashable_filter']);
		
		$posts = $this->paginator->paginate(
			$this->postRepository->unApproved(),
			$page,
			$limit
		);
		
		return $this->render(
			'moderator/content.html.twig',
			[
				'pagination' => $posts,
			]
		);
	}
}