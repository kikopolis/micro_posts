<?php

declare(strict_types = 1);

namespace App\Controller\Comment;

use App\Controller\AbstractController;
use App\Controller\Concerns\DisableDoctrineFiltersConcern;
use App\Repository\CommentRepository;
use App\Security\Voter\Contracts\VotablesContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class Preview
 * @package App\Controller\Comment
 * @author  Kristo Leas <kristo.leas@gmail.com>
 */
class Preview extends AbstractController
{
	use DisableDoctrineFiltersConcern;
	
	/**
	 * @var EntityManagerInterface
	 */
	private EntityManagerInterface $entityManager;
	
	/**
	 * @var CommentRepository
	 */
	private CommentRepository $commentRepository;
	
	/**
	 * Preview constructor.
	 * @param   EntityManagerInterface   $entityManager
	 * @param   CommentRepository        $commentRepository
	 */
	public function __construct(
		EntityManagerInterface $entityManager,
		CommentRepository $commentRepository
	)
	{
		$this->entityManager     = $entityManager;
		$this->commentRepository = $commentRepository;
	}
	
	/**
	 * @Route(
	 *     "/comments/{id}/preview",
	 *     name="comment.preview",
	 *     methods={"GET"},
	 *     requirements={"id"="\d+"}
	 * )
	 * @param   int   $id
	 * @return Response
	 */
	public function __invoke(int $id): Response
	{
		$this->disableAllDoctrineFilters();
		
		$comment = $this->commentRepository->find($id);
		
		if (! $comment
			|| ! $this->isGranted(VotablesContract::VIEW, $comment)) {
			
			throw $this->createNotFoundException('Comment not found.');
		}
		
		return $this->render(
			'comment/preview.html.twig',
			[
				'comment' => $comment,
			]
		);
	}
}