<?php

declare(strict_types = 1);

namespace App\Controller\Post;

use App\Controller\AbstractController;
use App\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Search
 * @package App\Controller\Post
 */
class Search extends AbstractController
{
	/**
	 * @var PostRepository
	 */
	private PostRepository $postRepository;
	
	/**
	 * @var PaginatorInterface
	 */
	private PaginatorInterface $paginator;
	
	/**
	 * Search constructor.
	 * @param  PostRepository      $postRepository
	 * @param  PaginatorInterface  $paginator
	 */
	public function __construct(
		PostRepository $postRepository,
		PaginatorInterface $paginator
	)
	{
		$this->postRepository = $postRepository;
		$this->paginator      = $paginator;
	}
	
	/**
	 * @Route(
	 *     "/posts/search/{page}",
	 *     name="posts.search",
	 *     methods={"POST"},
	 *     defaults={"page"=1},
	 *     requirements={"page"="\d+"}
	 * )
	 * @param  Request  $request
	 * @param  int      $page
	 * @return Response
	 */
	public function __invoke(Request $request, int $page = 1): Response
	{
		$term = $request->request->get('term') ?? null;
		
		if (! $term) {
			
			return $this->json(
				null,
				Response::HTTP_NO_CONTENT
			);
		}
		
		$page = (int) $request->get('page') ?? $page;
		
		$limit = $request->get('limit');
		
		$limit = $limit ? (int) $limit : 10;
		
		$results = $this->paginator->paginate(
			$this->postRepository->search($term),
			$page,
			$limit
		);
		
		return $this->json(
			[
				'posts' => $results,
			],
			Response::HTTP_OK
		);
	}
}