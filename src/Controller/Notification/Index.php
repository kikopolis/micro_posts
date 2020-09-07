<?php

declare(strict_types = 1);

namespace App\Controller\Notification;

use App\Controller\AbstractController;
use App\Entity\User;
use App\Repository\NotificationRepository;
use App\Security\Voter\Contracts\VotablesContract;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Index
 * @package App\Controller\Notification
 */
class Index extends AbstractController
{
	/**
	 * @var NotificationRepository
	 */
	private NotificationRepository $notificationRepository;
	
	/**
	 * Index constructor.
	 * @param  NotificationRepository  $notificationRepository
	 */
	public function __construct(NotificationRepository $notificationRepository)
	{
		$this->notificationRepository = $notificationRepository;
	}
	
	/**
	 * @Route(
	 *     "/notifications",
	 *     name="notifications.index",
	 *     methods={"GET"}
	 * )
	 * @return Response
	 */
	public function __invoke(): Response
	{
		$this->denyAccessUnlessGranted(User::ROLE_USER);
		
		/** @var User $user */
		$user = $this->getUser();
		
		$newNotes = $this->notificationRepository->newNotes($user);
		$oldNotes = $this->notificationRepository->oldNotes($user);
		
		foreach ($newNotes as $newNote) {
			
			$this->denyAccessUnlessGranted(
				VotablesContract::VIEW,
				$newNote
			);
		}
		
		foreach ($oldNotes as $oldNote) {
			
			$this->denyAccessUnlessGranted(
				VotablesContract::VIEW,
				$oldNote
			);
		}
		
		return $this->render(
			'notification/index.html.twig',
			[
				'new' => $newNotes,
				'old' => $oldNotes,
			]
		);
	}
}