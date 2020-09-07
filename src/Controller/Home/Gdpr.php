<?php

declare(strict_types = 1);

namespace App\Controller\Home;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Gdpr
 * @package App\Controller\Home
 */
class Gdpr extends AbstractController
{
	/**
	 * @Route(
	 *     "/gdpr",
	 *     name="gdpr",
	 *     methods={"GET"}
	 * )
	 * @return Response
	 */
	public function __invoke(): Response
	{
		return $this->render(
			'home/gdpr.html.twig'
		);
	}
}