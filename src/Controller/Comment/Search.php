<?php

declare(strict_types = 1);

namespace App\Controller\Comment;

use App\Controller\AbstractController;
use App\Repository\CommentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Search
 * @package App\Controller\Comment
 */
class Search extends AbstractController
{
	/**
	 * @var CommentRepository
	 */
	private CommentRepository $commentRepository;
	
	/**
	 * @var PaginatorInterface
	 */
	private PaginatorInterface $paginator;
	
	/**
	 * Search constructor.
	 * @param  CommentRepository   $commentRepository
	 * @param  PaginatorInterface  $paginator
	 */
	public function __construct(
		CommentRepository $commentRepository,
		PaginatorInterface $paginator
	)
	{
		$this->commentRepository = $commentRepository;
		$this->paginator         = $paginator;
	}
	
	/**
	 * @Route(
	 *     "/comments/search/{page}",
	 *     name="comments.search",
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
			$this->commentRepository->search($term),
			$page,
			$limit
		);
		
		return $this->json(
			[
				'comments' => $results,
			],
			Response::HTTP_OK
		);
	}
}