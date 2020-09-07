<?php

declare(strict_types = 1);

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Controller\Concerns\HomeWithFlashConcern;
use App\Entity\User;
use App\Event\User\PasswordHashEvent;
use App\Form\UserChangePasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PasswordChange
 * @package App\Controller\User
 */
class PasswordChange extends AbstractController
{
	use HomeWithFlashConcern;
	
	/** @var UserRepository */
	private UserRepository $userRepository;
	
	/** @var EntityManagerInterface */
	private EntityManagerInterface $entityManager;
	
	/**
	 * @var EventDispatcherInterface
	 */
	private EventDispatcherInterface $eventDispatcher;
	
	/**
	 * TokenPasswordChangeController constructor.
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
	 *     "/{token}/change-password",
	 *     name="change.password.with.token",
	 *     methods={"GET", "POST"},
	 *     requirements={"token"="[a-zA-Z0-9]+"}
	 * )
	 * @param  string   $token
	 * @param  Request  $request
	 * @return Response
	 */
	public function __invoke(string $token, Request $request): Response
	{
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_ANONYMOUSLY');
		
		/** @var User $user */
		$user = $this->userRepository->findOneBy(
			[
				'passwordResetToken' => $token,
			]
		);
		
		// If no user is found for the token, return immediately.
		if (! $user) {
			
			return $this->errorFlashHome(
				'The token is wrong or expired. Please try the process again.'
			);
		}
		
		$form = $this->createForm(UserChangePasswordType::class, $user);
		
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			
			$this->eventDispatcher->dispatch(new PasswordHashEvent($user));
			
			$this->entityManager->flush();
			
			return $this->successFlashHome(
				'Your password has been changed successfully. You may now log in with the new password.'
			);
		}
		
		return $this->render(
			'base/pure-form.html.twig',
			[
				'form'      => $form->createView(),
				'title'=>'Change your password',
				'message'=>'Enter a new password according to security guidelines'
			]
		);
	}
}