<?php

declare(strict_types = 1);

namespace App\Controller\Profile;

use App\Controller\AbstractController;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Myself
 * @package App\Controller\Profile
 */
class Myself extends AbstractController
{
	/**
	 * @Route(
	 *     "/profile",
	 *     name="profile.self",
	 *     methods={"GET"}
	 * )
	 * @return Response
	 */
	public function __invoke(): Response
	{
		$this->denyAccessUnlessGranted(User::ROLE_USER);
		
		return $this->redirectToRoute(
			'profile.by.username',
			[
				'username' => $this->getUser()->getUsername(),
			]
		);
	}
}