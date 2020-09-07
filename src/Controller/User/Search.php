<?php

declare(strict_types = 1);

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Search
 * @package App\Controller\User
 */
class Search extends AbstractController
{
	/**
	 * @var UserRepository
	 */
	private UserRepository $userRepository;
	
	/**
	 * @var PaginatorInterface
	 */
	private PaginatorInterface $paginator;
	
	/**
	 * Search constructor.
	 * @param  UserRepository      $userRepository
	 * @param  PaginatorInterface  $paginator
	 */
	public function __construct(
		UserRepository $userRepository,
		PaginatorInterface $paginator
	)
	{
		$this->userRepository = $userRepository;
		$this->paginator      = $paginator;
	}
	
	/**
	 * @Route(
	 *     "/users/search/{page}",
	 *     name="user.search",
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
		$term = $request->request->get('simple_search')['query'] ?? null;
		
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
			$this->userRepository->search($term),
			$page,
			$limit
		);
		
		return $this->json(
			[
				'users' => $results,
			],
			Response::HTTP_OK
		);
	}
}