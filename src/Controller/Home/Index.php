<?php

declare(strict_types = 1);

namespace App\Controller\Home;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Index
 * @package App\Controller\Home
 */
class Index extends AbstractController
{
	/**
	 * @Route(
	 *     "/",
	 *     name="homepage",
	 *     methods={"GET"}
	 * )
	 * @return Response
	 */
	public function __invoke(): Response
	{
		return $this->render(
			'home/index.html.twig'
		);
	}
}