<?php

declare(strict_types = 1);

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Entity\User;
use App\Event\User\EmailSecurityEvent;
use App\Event\TimeStampableUpdatedEvent;
use App\Event\User\PasswordHashEvent;
use App\Event\User\PasswordSecurityEvent;
use App\Form\UserEditType;
use App\Security\Voter\Contracts\VotablesContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Edit
 * @package App\Controller\User
 */
class Update extends AbstractController
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
	 * Edit constructor.
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
	 *     "/users/self/update",
	 *     name="user.edit",
	 *     methods={"GET", "POST"}
	 * )
	 * @param  Request  $request
	 * @return Response
	 */
	public function __invoke(Request $request): Response
	{
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
		
		/** @var User $user */
		$user = $this->getUser();
		
		$this->denyAccessUnlessGranted(VotablesContract::EDIT, $user);
		
		// save old email and password
		$oldEmail    = $user->getEmail();
		$oldPassword = $user->getPassword();
		
		$form = $this->createForm(UserEditType::class, $user);
		
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			
			$this->eventDispatcher->dispatch(new TimeStampableUpdatedEvent($user));
			
			if ($oldEmail !== $user->getEmail()) {
				
				$user->setOldEmail($oldEmail);
				
				$this->eventDispatcher->dispatch(new EmailSecurityEvent($user));
			}
			
			if ($oldPassword !== $user->getPlainPassword()) {
				
				$this->eventDispatcher->dispatch(new PasswordHashEvent($user));
				$this->eventDispatcher->dispatch(new PasswordSecurityEvent($user));
				
				$this->entityManager->flush();
				
				return $this->redirectToRoute('logout');
			}
			
			$this->entityManager->flush();
			
			return $this->redirectToRoute('user.show');
		}
		
		return $this->render(
			'users/update-account.html.twig',
			[
				'form'  => $form->createView(),
				'title' => 'Modify account',
			]
		);
	}
}