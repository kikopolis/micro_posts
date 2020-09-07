<?php

declare(strict_types = 1);

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Show
 * @package App\Controller\User
 */
class Show extends AbstractController
{
	/**
	 * @Route(
	 *     "/users",
	 *     name="user.show",
	 *     methods={"GET"}
	 * )
	 * @return Response
	 */
	public function __invoke(): Response
	{
		$this->denyAccessUnlessGranted(User::ROLE_USER);
		
		return $this->render(
			'users/show.html.twig',
			[
				'user' => $this->getUser(),
			]
		);
	}
}