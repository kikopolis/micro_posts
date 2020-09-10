<?php

declare(strict_types = 1);

namespace App\Controller\Comment;

use App\Controller\AbstractController;
use App\Controller\Concerns\ExpectsJsonConcern;
use App\Entity\Comment;
use App\Entity\User;
use App\Event\Comment\ReportEvent;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use App\Security\Voter\Contracts\VotablesContract;
use App\Service\Contracts\FlashContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Report
 * @package App\Controller\Comment
 * @author  Kristo Leas <kristo.leas@gmail.com>
 */
class Report extends AbstractController
{use ExpectsJsonConcern;
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
	 * @var CommentRepository
	 */
	private CommentRepository $commentRepository;
	
	/**
	 * Report constructor.
	 * @param   EntityManagerInterface     $entityManager
	 * @param   EventDispatcherInterface   $eventDispatcher
	 * @param   UserRepository             $userRepository
	 * @param   CommentRepository          $commentRepository
	 */
	public function __construct(
		EntityManagerInterface $entityManager,
		EventDispatcherInterface $eventDispatcher,
		UserRepository $userRepository,
	CommentRepository $commentRepository
	)
	{
		$this->entityManager   = $entityManager;
		$this->eventDispatcher = $eventDispatcher;
		$this->userRepository  = $userRepository;
		$this->commentRepository = $commentRepository;
	}
	
	/**
	 * @Route(
	 *     "/comments/{id}/report",
	 *     name="comment.report",
	 *     methods={"GET", "POST"},
	 *     requirements={"id"="\d+"}
	 * )
	 * @param   int       $id
	 * @param   Request   $request
	 * @return Response
	 */
	public function __invoke(int $id, Request $request): Response
	{
		$comment = $this->commentRepository->find($id);
		
		if (! $comment) {
			
			if ($this->expectsJson($request)) {
				
				return $this->json(
					null,
					Response::HTTP_NOT_FOUND
				);
			}
			
			throw $this->createNotFoundException('Comment not found.');
		}
		
		if (! $this->isGranted(User::ROLE_USER)) {
			
			if ($this->expectsJson($request)) {
				
				return $this->json(
					null,
					Response::HTTP_BAD_REQUEST
				);
			}
			
			$this->addFlash(
				FlashContract::WARNING,
				'Please login for this action.'
			);
			
			$this->redirectToRoute('login');
		}
		
		$mods = $this->userRepository->findBy(['roles' => [User::ROLE_MODERATOR]]);
		
		$this->eventDispatcher->dispatch(
			new ReportEvent(
				$mods,
				$this->getUser(),
				$comment
			)
		);
		
		// Flush all together with notifications
		$this->entityManager->flush();
		
		if ($this->expectsJson($request)) {
			
			return $this->json(
				null,
				Response::HTTP_NO_CONTENT
			);
		}
		
		$this->addFlash(
			FlashContract::SUCCESS,
			'Comment reported as inappropriate. A mod will review it asap.'
		);
		
		return $this->redirectToRoute(
			'post.show',
			[
				'id' => $comment->getPost()->getId(),
			]
		);
	}
}