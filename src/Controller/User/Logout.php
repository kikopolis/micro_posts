<?php

declare(strict_types = 1);

namespace App\Controller\User;

use App\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Logout
 * @package App\Controller\User
 */
class Logout extends AbstractController
{
	/**
	 * @Route(
	 *     "/logout",
	 *     name="logout"
	 * )
	 */
	public function __invoke()
	{
	}
}