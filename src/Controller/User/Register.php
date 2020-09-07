<?php

declare(strict_types = 1);

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Controller\Concerns\HomeWithFlashConcern;
use App\Entity\User;
use App\Event\User\PasswordHashEvent;
use App\Event\TimeStampableCreatedEvent;
use App\Event\User\CreateEvent;
use App\Form\UserRegisterType;
use App\Security\Voter\Contracts\VotablesContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Register
 * @package App\Controller\User
 */
class Register extends AbstractController
{
	use HomeWithFlashConcern;
	
	/**
	 * @var EventDispatcherInterface
	 */
	private EventDispatcherInterface $eventDispatcher;
	
	/**
	 * @var EntityManagerInterface
	 */
	private EntityManagerInterface $entityManager;
	
	/**
	 * RegisterController constructor.
	 * @param  EventDispatcherInterface  $eventDispatcher
	 * @param  EntityManagerInterface    $entityManager
	 */
	public function __construct(
		EventDispatcherInterface $eventDispatcher,
		EntityManagerInterface $entityManager
	)
	{
		$this->eventDispatcher = $eventDispatcher;
		$this->entityManager   = $entityManager;
	}
	
	/**
	 * @Route(
	 *     "/register",
	 *     name="register",
	 *     methods={"GET", "POST"}
	 * )
	 * @param  Request  $request
	 * @return Response
	 */
	public function __invoke(Request $request): Response
	{
		$user = new User();
		
		if (! $this->isGranted(VotablesContract::CREATE, $user)) {
			
			return $this->redirectToRoute('homepage');
		}
		
		$form = $this->createForm(UserRegisterType::class, $user);
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			
			$this->eventDispatcher->dispatch(
				new PasswordHashEvent($user)
			);
			
			$this->eventDispatcher->dispatch(
				new CreateEvent($user)
			);
			
			$this->eventDispatcher->dispatch(
				new TimeStampableCreatedEvent($user)
			);
			
			$this->entityManager->persist($user);
			$this->entityManager->flush();
			
			return $this->successFlashHome('Account created! We have sent you an email with instructions to activate your account.');
		}
		
		return $this->render(
			'register/register.html.twig',
			[
				'form' => $form->createView(),
			]
		);
	}
}