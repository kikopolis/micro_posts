<?php

declare(strict_types = 1);

namespace App\Controller\Moderator;

use App\Controller\AbstractController;
use App\Controller\Concerns\HomeWithFlashConcern;
use App\Entity\User;
use App\Repository\NotificationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Index
 * @package App\Controller\Moderator
 */
class Index extends AbstractController
{
	use HomeWithFlashConcern;
	
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
	 *     "/moderator/index",
	 *     name="mod.index",
	 *     methods={"GET"}
	 * )
	 * @return Response
	 */
	public function __invoke(): Response
	{
		$this->denyAccessUnlessGranted(User::ROLE_MODERATOR);
		
		if (! $this->isGranted('IS_AUTHENTICATED_FULLY')) {
			
			return $this->errorFlashHome(
				'You must be fully logged in for moderator functions.
			Please log in again.'
			);
		}
		
		return $this->render(
			'moderator/index.html.twig',
			[
				'notifications' => $this->notificationRepository->modNotes($this->getUser()),
			]
		);
	}
}