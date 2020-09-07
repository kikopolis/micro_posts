<?php

declare(strict_types = 1);

namespace App\Controller\Admin\User;

use App\Controller\AbstractController;
use App\Controller\Concerns\DisableDoctrineFiltersConcern;
use App\Entity\User;
use App\Event\User\DeleteEvent;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class Delete
 * @package App\Controller\Admin\User
 */
class Delete extends AbstractController
{
	use DisableDoctrineFiltersConcern;
	
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
	 * Delete constructor.
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
	 *     "/admin/users/{id}/delete",
	 *     name="admin.user.delete",
	 *     methods={"DELETE"},
	 *     requirements={"id"="\d+"}
	 * )
	 * @param  int  $id
	 * @return Response
	 */
	public function __invoke(int $id): Response
	{
		if (
			! $this->isGranted(User::ROLE_ADMINISTRATOR)
			|| ! $this->isGranted('IS_AUTHENTICATED_FULLY')
		) {
			
			return $this->json(
				[
					'message' => 'You must be fully logged in for admin functions.
			Please log in again.',
				],
				Response::HTTP_UNAUTHORIZED
			);
		}
		
		$this->disable('trashable_filter');
		
		$user = $this->userRepository->find($id);
		
		if (! $user) {
			
			return $this->json(null, Response::HTTP_NOT_FOUND);
		}
		
		$this->eventDispatcher->dispatch(
			new DeleteEvent($user, $this->getUser())
		);
		
		$this->entityManager->flush();
		
		return $this->redirectToRoute('logout');
	}
}