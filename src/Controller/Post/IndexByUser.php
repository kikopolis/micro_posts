<?php

declare(strict_types = 1);

namespace App\Controller\Post;

use App\Controller\AbstractController;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class IndexByUser
 * @package App\Controller\Post
 */
class IndexByUser extends AbstractController
{
	/**
	 * @var PaginatorInterface
	 */
	private PaginatorInterface $paginator;
	
	/**
	 * @var PostRepository
	 */
	private PostRepository $postRepository;
	
	/**
	 * ShowByUser constructor.
	 * @param  PaginatorInterface  $paginator
	 * @param  PostRepository      $postRepository
	 */
	public function __construct(
		PaginatorInterface $paginator,
		PostRepository $postRepository
	)
	{
		$this->paginator      = $paginator;
		$this->postRepository = $postRepository;
	}
	
	/**
	 * @Route(
	 *     "/posts/{username}/{page}",
	 *     name="posts.for.user",
	 *     methods={"GET"},
	 *     requirements={"username"="\w+", "page"="\d+"},
	 *     defaults={"page"=1}
	 * )
	 * @param  Request  $request
	 * @param  User     $user
	 * @param  int      $page
	 * @return Response
	 */
	public function __invoke(Request $request, User $user, int $page = 1): Response
	{
		$qb = $this->postRepository->allFor($user);
		
		$page = (int) $request->get('page') ?? $page;
		
		$limit = $request->get('limit');
		
		$limit = $limit ? (int) $limit : Post::PAGINATION_PER_PAGE;
		
		$posts = $this->paginator->paginate(
			$qb,
			$page,
			$limit
		);
		
		return $this->render(
			'post/index.html.twig',
			[
				'pagination' => $posts,
				'title'      => "Posts for user {$user->getFullName()}",
			]
		);
	}
}