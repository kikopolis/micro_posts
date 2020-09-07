<?php

declare(strict_types = 1);

namespace App\Controller\User;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class Login
 * @package App\Controller\User
 */
class Login extends AbstractController
{
	/**
	 * @var AuthenticationUtils
	 */
	private AuthenticationUtils $authenticationUtils;
	
	/**
	 * Login constructor.
	 * @param  AuthenticationUtils  $authenticationUtils
	 */
	public function __construct(AuthenticationUtils $authenticationUtils)
	{
		$this->authenticationUtils = $authenticationUtils;
	}
	
	/**
	 * @Route(
	 *     "/login",
	 *     name="login"
	 * )
	 * @return Response
	 */
	public function __invoke(): Response
	{
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_ANONYMOUSLY');
		
		return $this->render(
			'security/login.html.twig',
			[
				'error'        => $this->authenticationUtils->getLastAuthenticationError(),
				'lastUsername' => $this->authenticationUtils->getLastUsername(),
			]
		);
	}
}