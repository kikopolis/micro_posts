<?php

declare(strict_types = 1);

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Controller\Concerns\HomeWithFlashConcern;
use App\Entity\User;
use App\Event\User\PasswordTokenEvent;
use App\Form\UserNewActionCodeType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ForgottenPassword
 * @package App\Controller\User
 */
class ForgottenPassword extends AbstractController
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
	 * ForgotPasswordController constructor.
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
	 *     "/forgot-password",
	 *     name="forgot.password",
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
			
			$message =
				'You have successfully requested a password reset token. Please check your email and '
				. 'follow the instructions. It may take some time for the email to arrive.';
			
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
			
			$this->eventDispatcher->dispatch(
				new PasswordTokenEvent($user)
			);
			
			$this->entityManager->flush();
			
			// For security, any request either successful or not meaning the user exists or not will get a success page shown.
			return $this->successFlashHome($message);
		}
		
		return $this->render(
			'base/pure-form.html.twig',
			[
				'form'    => $form->createView(),
				'title'   => 'Request a password reset',
				'message' => 'Enter your account email and we will send you a verification token that you can use to change your password.',
			]
		);
	}
}