<?php

declare(strict_types = 1);

namespace App\Controller\Profile;

use App\Controller\AbstractController;
use App\Entity\User;
use App\Event\TimeStampableUpdatedEvent;
use App\Form\ProfileEditType;
use App\Kikopolis\Str;
use App\Security\Voter\Contracts\VotablesContract;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Update
 * @package App\Controller\Profile
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
	 * @var string
	 */
	private string $uploadDirectory;
	
	/**
	 * Edit constructor.
	 * @param  EntityManagerInterface    $entityManager
	 * @param  EventDispatcherInterface  $eventDispatcher
	 * @param  string                    $uploadDirectory
	 */
	public function __construct(
		EntityManagerInterface $entityManager,
		EventDispatcherInterface $eventDispatcher,
		string $uploadDirectory
	)
	{
		$this->entityManager   = $entityManager;
		$this->eventDispatcher = $eventDispatcher;
		$this->uploadDirectory = $uploadDirectory;
	}
	
	/**
	 * @Route(
	 *     "/profile/self/update",
	 *     name="profile.edit",
	 *     methods={"GET", "POST"}
	 * )
	 * @param  Request  $request
	 * @return Response
	 * @throws Exception
	 */
	public function __invoke(Request $request): Response
	{
		$this->denyAccessUnlessGranted(User::ROLE_USER);
		
		/** @var User $user */
		$user = $this->getUser();
		
		$profile = $user->getProfile();
		
		$this->denyAccessUnlessGranted(VotablesContract::EDIT, $profile);
		
		$form = $this->createForm(ProfileEditType::class, $profile);
		
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			
			/** @var UploadedFile $avatar */
			$avatar = $form['avatar']->getData();
			
			if ($avatar) {
				
				$avatarPath = $avatar->move(
					$this->uploadDirectory,
					Str::random(48) . '.' . $avatar->guessClientExtension()
				);
				
				$profile->setAvatar($avatarPath->getRealPath());
			}
			
			$this->eventDispatcher->dispatch(new TimeStampableUpdatedEvent($user));
			
			$this->entityManager->flush();
			
			return $this->redirectToRoute('profile.self');
		}
		
		return $this->render(
			'users/update-profile.html.twig',
			[
				'form' => $form->createView(),
			]
		);
	}
}