<?php

declare(strict_types = 1);

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Controller\Concerns\ExpectsJsonConcern;
use App\Entity\User;
use App\Event\User\TrashEvent;
use App\Service\Contracts\FlashContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class Trash
 * @package App\Controller\User
 */
class Trash extends AbstractController
{
	use ExpectsJsonConcern;
	
	/**
	 * @var EntityManagerInterface
	 */
	private EntityManagerInterface $entityManager;
	
	/**
	 * @var EventDispatcherInterface
	 */
	private EventDispatcherInterface $eventDispatcher;
	
	/**
	 * Delete constructor.
	 * @param  EntityManagerInterface    $entityManager
	 * @param  EventDispatcherInterface  $eventDispatcher
	 */
	public function __construct(
		EntityManagerInterface $entityManager,
		EventDispatcherInterface $eventDispatcher
	)
	{
		$this->entityManager   = $entityManager;
		$this->eventDispatcher = $eventDispatcher;
	}
	
	/**
	 * @Route(
	 *     "/users/trash",
	 *     name="user.trash.self",
	 *     methods={"GET", "POST"}
	 * )
	 * @param  Request  $request
	 * @return Response
	 */
	public function __invoke(Request $request): Response
	{
		if (
			! $this->isGranted(User::ROLE_USER)
			|| ! $this->isGranted('IS_AUTHENTICATED_FULLY')
		) {
			
			if ($this->expectsJson($request)) {
				
				return $this->json(
					[
						'message' => 'You must be logged in to do this action',
					],
					Response::HTTP_UNAUTHORIZED
				);
			}
			
			$this->addFlash(
				FlashContract::ERROR,
				'You must be logged in to do this action'
			);
			
			return $this->redirectToRoute('homepage');
		}
		
		/** @var User $user */
		$user = $this->getUser();
		
		if (! $user->isTrashed()) {
			
			$this->eventDispatcher->dispatch(
				new TrashEvent(
					$user,
					$user
				)
			);
			
			$this->entityManager->flush();
		}
		
		if ($this->expectsJson($request)) {
			
			return $this->json(
				null,
				Response::HTTP_NO_CONTENT
			);
		}
		
		$this->addFlash(
			FlashContract::SUCCESS,
			'You have successfully trashed your account. You may no longer post and your account will be deleted automatically.'
		);
		
		return $this->redirectToRoute('user.show');
	}
}