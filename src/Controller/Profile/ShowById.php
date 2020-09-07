<?php

declare(strict_types = 1);

namespace App\Controller\Profile;

use App\Controller\AbstractController;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ShowById
 * @package App\Controller\Profile
 */
class ShowById extends AbstractController
{
	/**
	 * @Route(
	 *     "/profile/{id}",
	 *     name="profile.by.id",
	 *     methods={"GET"},
	 *     requirements={"id"="\d+"}
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