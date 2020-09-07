<?php

declare(strict_types = 1);

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Controller\Concerns\ExpectsJsonConcern;
use App\Entity\User;
use App\Event\User\UnFollowEvent;
use App\Service\Contracts\FlashContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class UnFollow
 * @package App\Controller\User
 */
class UnFollow extends AbstractController
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
	 * Follow constructor.
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
	 *     "/users/{username}/un-follow",
	 *     name="un.follow.user",
	 *     methods={"GET", "POST"},
	 *     requirements={"username"="\w+"}
	 * )
	 * @param  User     $user
	 * @param  Request  $request
	 * @return Response
	 */
	public function __invoke(User $user, Request $request): Response
	{
		if (! $this->isGranted(User::ROLE_USER)) {
			
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
				'You must be logged in for this action'
			);
			
			return $this->redirectToRoute('login');
		}
		
		$this->eventDispatcher->dispatch(
			new UnFollowEvent(
				$this->getUser(),
				$user
			)
		);
		
		$this->entityManager->flush();
		
		if ($this->expectsJson($request)) {
			
			return $this->json(
				null,
				Response::HTTP_NO_CONTENT
			);
		}
		
		return $this->redirectToRoute(
			'profile.by.username',
			[
				'username' => $user->getUsername(),
			]
		);
	}
}