<?php

declare(strict_types = 1);

namespace App\Controller\Home;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Support
 * @package App\Controller\Home
 */
class Support extends AbstractController
{
	/**
	 * @Route(
	 *     "/support",
	 *     name="support",
	 *     methods={"GET"}
	 * )
	 * @return Response
	 */
	public function __invoke(): Response
	{
		return $this->render(
			'home/help.html.twig'
		);
	}
}