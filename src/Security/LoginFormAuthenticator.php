<?php

declare(strict_types = 1);

namespace App\Security;

use App\Entity\User;
use App\Service\Contracts\FlashContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Class LoginFormAuthenticator
 * @package App\Security
 */
class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
	use TargetPathTrait;
	
	/**
	 * @var EntityManagerInterface
	 */
	private EntityManagerInterface $entityManager;
	
	/**
	 * @var RouterInterface
	 */
	private RouterInterface $router;
	
	/**
	 * @var CsrfTokenManagerInterface
	 */
	private CsrfTokenManagerInterface $tokenManager;
	
	/**
	 * @var UserPasswordEncoderInterface
	 */
	private UserPasswordEncoderInterface $passwordEncoder;
	
	/**
	 * @var SessionInterface
	 */
	private SessionInterface $session;
	
	/**
	 * LoginFormAuthenticator constructor.
	 * @param  EntityManagerInterface        $entityManager
	 * @param  RouterInterface               $router
	 * @param  CsrfTokenManagerInterface     $tokenManager
	 * @param  UserPasswordEncoderInterface  $passwordEncoder
	 * @param  SessionInterface              $session
	 */
	public function __construct(
		EntityManagerInterface $entityManager,
		RouterInterface $router,
		CsrfTokenManagerInterface $tokenManager,
		UserPasswordEncoderInterface $passwordEncoder,
		SessionInterface $session
	)
	{
		$this->entityManager   = $entityManager;
		$this->router          = $router;
		$this->tokenManager    = $tokenManager;
		$this->passwordEncoder = $passwordEncoder;
		$this->session         = $session;
	}
	
	/**
	 * @return string
	 */
	protected function getLoginUrl(): string
	{
		return $this->router->generate('login');
	}
	
	/**
	 * @param  Request  $request
	 * @return bool
	 */
	public function supports(Request $request): bool
	{
		return 'login' === $request->attributes->get('_route')
			&& $request->isMethod('POST');
	}
	
	/**
	 * @param  Request  $request
	 * @return array
	 */
	public function getCredentials(Request $request): array
	{
		$credentials = [
			'username'   => $request->request->get('_username'),
			'password'   => $request->request->get('_password'),
			'csrf_token' => $request->request->get('_csrf_token'),
		];
		
		$request->getSession()->set(
			Security::LAST_USERNAME,
			$credentials['username']
		)
		;
		
		return $credentials;
	}
	
	/**
	 * @param  mixed                  $credentials
	 * @param  UserProviderInterface  $userProvider
	 * @return User
	 * @throws InvalidCsrfTokenException|CustomUserMessageAuthenticationException
	 */
	public function getUser($credentials, UserProviderInterface $userProvider): User
	{
		$token = new CsrfToken(
			'authenticate',
			$credentials['csrf_token']
		);
		
		if (! $this->tokenManager->isTokenValid($token)) {
			
			throw new InvalidCsrfTokenException();
		}
		
		$user = $this->entityManager
			->getRepository(User::class)
			->findOneBy(
				[
					'username' => $credentials['username'],
				]
			)
		;
		
		if (! $user) {
			
			throw new CustomUserMessageAuthenticationException(
				'Username could not be found.'
			);
		}
		
		return $user;
	}
	
	/**
	 * @param  mixed          $credentials
	 * @param  UserInterface  $user
	 * @return bool
	 */
	public function checkCredentials($credentials, UserInterface $user): bool
	{
		return $this->passwordEncoder->isPasswordValid(
			$user,
			$credentials['password']
		);
	}
	
	/**
	 * @param  Request         $request
	 * @param  TokenInterface  $token
	 * @param  string          $providerKey
	 * @return Response
	 */
	public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): Response
	{
		$this->session->getFlashBag()->add(
			FlashContract::SUCCESS,
			sprintf(
				'Welcome back, %s! You are now logged in!',
				$token->getUser()->getFullName()
			)
		)
		;
		
		$targetPath = $this->getTargetPath(
			$request->getSession(),
			$providerKey
		);
		
		if ($targetPath) {
			
			return new RedirectResponse($targetPath);
		}
		
		return new RedirectResponse($this->router->generate('homepage'));
	}
}