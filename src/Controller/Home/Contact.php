<?php

declare(strict_types = 1);

namespace App\Controller\Home;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Contact
 * @package App\Controller\Home
 */
class Contact extends AbstractController
{
	/**
	 * @Route(
	 *     "/contact",
	 *     name="contact",
	 *     methods={"GET"}
	 * )
	 * @return Response
	 */
	public function __invoke(): Response
	{
		return $this->render(
			'home/contact.html.twig'
		);
	}
}