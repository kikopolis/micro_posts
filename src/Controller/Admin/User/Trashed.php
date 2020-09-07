<?php

declare(strict_types = 1);

namespace App\Controller\Admin\User;

use App\Controller\AbstractController;
use App\Controller\Concerns\DisableDoctrineFiltersConcern;
use App\Controller\Concerns\HomeWithFlashConcern;
use App\Entity\User;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Trashed
 * @package App\Controller\Admin\User
 */
class Trashed extends AbstractController
{
	use HomeWithFlashConcern;
	use DisableDoctrineFiltersConcern;
	
	/**
	 * @var UserRepository
	 */
	private UserRepository $userRepository;
	
	/**
	 * @var PaginatorInterface
	 */
	private PaginatorInterface $paginator;
	
	/**
	 * Trashed constructor.
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
	 *     "/admin/users/trashed/{page}/{limit}",
	 *     name="admin.users.trashed",
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
		$this->denyAccessUnlessGranted(User::ROLE_ADMINISTRATOR);
		
		if (! $this->isGranted('IS_AUTHENTICATED_FULLY')) {
			
			return $this->errorFlashHome(
				'You must be fully logged in for moderator functions.
			Please log in again.'
			);
		}
		
		$this->disable('trashable_filter');
		
		$users = $this->paginator->paginate(
			$this->userRepository->trashed(),
			$page,
			$limit
		);
		
		return $this->render(
			'moderator/content.html.twig',
			[
				'users' => $users,
			]
		);
	}
}