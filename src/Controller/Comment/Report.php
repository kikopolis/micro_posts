<?php

declare(strict_types = 1);

namespace App\Controller\Comment;

use App\Controller\AbstractController;
use App\Entity\Comment;
use App\Entity\User;
use App\Event\Comment\ReportEvent;
use App\Repository\UserRepository;
use App\Service\Contracts\FlashContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Report extends AbstractController
{
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
	 * Report constructor.
	 * @param  EntityManagerInterface    $entityManager
	 * @param  EventDispatcherInterface  $eventDispatcher
	 * @param  UserRepository            $userRepository
	 */
	public function __construct(
		EntityManagerInterface $entityManager,
		EventDispatcherInterface $eventDispatcher,
		UserRepository $userRepository
	)
	{
		$this->entityManager   = $entityManager;
		$this->eventDispatcher = $eventDispatcher;
		$this->userRepository  = $userRepository;
	}
	
	/**
	 * @Route(
	 *     "/comments/{id}/report",
	 *     name="comment.report",
	 *     methods={"POST"},
	 *     requirements={"id"="\d+"}
	 * )
	 * @param  Comment  $comment
	 * @return Response
	 */
	public function __invoke(Comment $comment): Response
	{
		if (! $this->isGranted('ROLE_USER')) {
			
			return $this->json(
				null,
				Response::HTTP_UNAUTHORIZED
			);
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
		
		$this->addFlash(
			FlashContract::SUCCESS,
			'Comment reported as inappropriate. A mod will review it asap.'
		);
		
		return $this->json(
			null,
			Response::HTTP_NO_CONTENT
		);
	}
}