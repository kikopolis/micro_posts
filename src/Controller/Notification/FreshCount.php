<?php

declare(strict_types = 1);

namespace App\Controller\Notification;

use App\Controller\AbstractController;
use App\Repository\NotificationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FreshCount
 * @package App\Controller\Notification
 */
class FreshCount extends AbstractController
{
	/**
	 * @var NotificationRepository
	 */
	private NotificationRepository $notificationRepository;
	
	/**
	 * FreshCount constructor.
	 * @param  NotificationRepository  $notificationRepository
	 */
	public function __construct(NotificationRepository $notificationRepository)
	{
		$this->notificationRepository = $notificationRepository;
	}
	
	/**
	 * @Route(
	 *     "/notifications/fresh/count",
	 *     name="notifications.fresh.count",
	 *     methods={"GET"}
	 * )
	 * @return Response
	 */
	public function __invoke(): Response
	{
		$user = $this->getUser();
		
		if (! $user) {
			
			return $this->json(
				null,
				Response::HTTP_NO_CONTENT
			);
		}
		
		return $this->json(
			[
				'count' => $this->notificationRepository->count(
					[
						'author' => $user,
						'seen'   => false,
					]
				),
			]
		);
	}
}