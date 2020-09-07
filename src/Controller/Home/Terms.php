<?php

declare(strict_types = 1);

namespace App\Controller\Home;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Terms
 * @package App\Controller\Home
 */
class Terms extends AbstractController
{
	/**
	 * @Route(
	 *     "/terms-and-conditions",
	 *     name="terms.and.conditions",
	 *     methods={"GET"}
	 * )
	 * @return Response
	 */
	public function __invoke(): Response
	{
		return $this->render(
			'home/terms.html.twig'
		);
	}
}