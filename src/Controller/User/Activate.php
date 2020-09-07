<?php

declare(strict_types = 1);

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Event\User\ActivationEvent;
use App\Repository\UserRepository;
use App\Service\Contracts\FlashContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Activate
 * @package App\Controller\User
 */
class Activate extends AbstractController
{
	/** @var UserRepository */
	private UserRepository $userRepository;
	
	/** @var EntityManagerInterface */
	private EntityManagerInterface $entityManager;
	
	/**
	 * @var EventDispatcherInterface
	 */
	private EventDispatcherInterface $eventDispatcher;
	
	/**
	 * AccountConfirmationController constructor.
	 * @param  UserRepository            $userRepository
	 * @param  EntityManagerInterface    $entityManager
	 * @param  EventDispatcherInterface  $eventDispatcher
	 */
	public function __construct(
		UserRepository $userRepository,
		EntityManagerInterface $entityManager,
		EventDispatcherInterface $eventDispatcher
	)
	{
		$this->userRepository  = $userRepository;
		$this->entityManager   = $entityManager;
		$this->eventDispatcher = $eventDispatcher;
	}
	
	/**
	 * @Route(
	 *     "/account/confirm/{token}",
	 *     name="activation.confirm.token",
	 *     methods={"GET"},
	 *     requirements={"token"="[a-zA-Z0-9]+"}
	 * )
	 * @param  string  $token
	 * @return Response
	 */
	public function __invoke(string $token): Response
	{
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_ANONYMOUSLY');
		
		$user = $this->userRepository->findOneBy(
			[
				'accountActivationToken' => $token,
			]
		);
		if ($user !== null) {
			
			$this->eventDispatcher->dispatch(new ActivationEvent($user));
			
			$this->entityManager->flush();
			
			$this->addFlash(
				FlashContract::SUCCESS,
				'Account activated. You may now login!'
			);
			
			return $this->redirectToRoute('login');
		} else {
			
			$this->addFlash(
				FlashContract::ERROR,
				'No user account found. Please try again.'
			);
		}
		
		return $this->redirectToRoute('new.account.activation.token');
	}
}