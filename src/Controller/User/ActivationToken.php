<?php

declare(strict_types = 1);

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Controller\Concerns\HomeWithFlashConcern;
use App\Entity\User;
use App\Event\User\AccountActivationTokenEvent;
use App\Form\UserNewActionCodeType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ActivationToken
 * @package App\Controller\User
 */
class ActivationToken extends AbstractController
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
	 * RegisterTokenRequest constructor.
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
	 *     "/request-new-activation-token",
	 *     name="new.account.activation.token",
	 *     methods={"GET", "POST"}
	 * )
	 * @param  Request  $request
	 * @return Response
	 */
	public function __invoke(Request $request): Response
	{
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_ANONYMOUSLY');
		
		$form = $this->createForm(UserNewActionCodeType::class);
		
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			
			$message = 'The new code has been sent to your email. Give it some time and check the email!';
			
			/** @var User $user */
			$user = $this->userRepository->findOneBy(
				[
					'email' => $form['email']->getData(),
				]
			);
			
			// If no user is found for the email or the user is already active, for security, show the code was sent.
			if (! $user) {
				
				return $this->successFlashHome($message);
			}
			
			// If user is already activated, show message
			if ($user->isActivated()) {
				
				return $this->infoFlashHome(
					'This account is already activated. You do not need to request a new code and may log in.'
				);
			}
			
			$this->eventDispatcher->dispatch(
				new AccountActivationTokenEvent($user)
			);
			
			$this->entityManager->flush();
			
			// For security, any request either successful or not meaning the user exists or not will get a success page shown.
			return $this->successFlashHome($message);
		}
		
		return $this->render(
			'base/pure-form.html.twig',
			[
				'form'    => $form->createView(),
				'title'   => 'Get a new account activation token',
				'message' => 'Enter your email and we will send you a new code. Please allow some time for the email to arrive.',
			]
		);
	}
}