<?php

declare(strict_types = 1);

namespace App\Controller\Preferences;

use App\Controller\AbstractController;
use App\Controller\Concerns\HomeWithFlashConcern;
use App\Entity\User;
use App\Event\TimeStampableUpdatedEvent;
use App\Form\PreferencesEditType;
use App\Security\Voter\Contracts\VotablesContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class Update
 * @package App\Controller\Preferences
 */
class Update extends AbstractController
{
	use HomeWithFlashConcern;
	
	/**
	 * @var EntityManagerInterface
	 */
	private EntityManagerInterface $entityManager;
	
	/**
	 * @var EventDispatcherInterface
	 */
	private EventDispatcherInterface $eventDispatcher;
	
	/**
	 * EditController constructor.
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
	 *     "/preferences/edit",
	 *     name="preferences.edit",
	 *     methods={"GET", "POST"}
	 * )
	 * @param  Request  $request
	 * @return Response
	 */
	public function __invoke(Request $request): Response
	{
		$this->denyAccessUnlessGranted(User::ROLE_USER);
		
		/** @var User $user */
		$user = $this->getUser();
		
		$preferences = $user->getPreferences();
		
		$this->denyAccessUnlessGranted(VotablesContract::EDIT, $preferences);
		
		$form = $this->createForm(PreferencesEditType::class, $preferences);
		
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			
			$this->eventDispatcher->dispatch(new TimeStampableUpdatedEvent($user));
			
			$this->entityManager->flush();
			
			return $this->successFlashHome('Preferences saved successfully!');
		}
		
		return $this->render(
			'users/update-preferences.html.twig',
			[
				'form'  => $form->createView()
			]
		);
	}
}