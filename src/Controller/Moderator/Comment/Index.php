<?php

declare(strict_types = 1);

namespace App\Controller\Moderator\Comment;

use App\Controller\AbstractController;
use App\Controller\Concerns\DisableDoctrineFiltersConcern;
use App\Controller\Concerns\HomeWithFlashConcern;
use App\Entity\User;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Index
 * @package App\Controller\Moderator\Comment
 */
class Index extends AbstractController
{
	use HomeWithFlashConcern;
	use DisableDoctrineFiltersConcern;
	
	/**
	 * @var CommentRepository
	 */
	private CommentRepository $commentRepository;
	
	/**
	 * @var PaginatorInterface
	 */
	private PaginatorInterface $paginator;
	
	/**
	 * @var EntityManagerInterface
	 */
	private EntityManagerInterface $entityManager;
	
	/**
	 * Index constructor.
	 * @param  CommentRepository       $commentRepository
	 * @param  PaginatorInterface      $paginator
	 * @param  EntityManagerInterface  $entityManager
	 */
	public function __construct(
		CommentRepository $commentRepository,
		PaginatorInterface $paginator,
		EntityManagerInterface $entityManager
	)
	{
		$this->commentRepository = $commentRepository;
		$this->paginator         = $paginator;
		$this->entityManager     = $entityManager;
	}
	
	/**
	 * @Route(
	 *     "/moderator/comments/{page}/{limit}",
	 *     name="mod.comments.index",
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
		
		$comments = $this->paginator->paginate(
			$this->commentRepository->allForMod(),
			$page,
			$limit
		);
		
		return $this->render(
			'moderator/content.html.twig',
			[
				'pagination' => $comments,
			]
		);
	}
}