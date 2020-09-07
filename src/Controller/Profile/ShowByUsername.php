<?php

declare(strict_types = 1);

namespace App\Controller\Profile;

use App\Controller\AbstractController;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ShowByUsername
 * @package App\Controller\Profile
 */
class ShowByUsername extends AbstractController
{
	/**
	 * @Route(
	 *     "/profile/{username}",
	 *     name="profile.by.username",
	 *     methods={"GET"},
	 *     requirements={"username"="^[a-zA-Z][a-zA-Z0-9_]+[a-zA-Z0-9]+$"}
	 *     )
	 * @param  User  $user
	 * @return Response
	 */
	public function __invoke(User $user): Response
	{
		return $this->render(
			'users/profile/show.html.twig',
			[
				'user' => $user,
			]
		);
	}
}