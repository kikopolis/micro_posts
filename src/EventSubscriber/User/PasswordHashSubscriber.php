<?php

declare(strict_types = 1);

namespace App\EventSubscriber\User;

use App\Event\User\PasswordHashEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

/**
 * Class PasswordHashSubscriber
 * @package App\EventSubscriber\User
 */
class PasswordHashSubscriber implements EventSubscriberInterface
{
	/**
	 * @var UserPasswordEncoderInterface
	 */
	private UserPasswordEncoderInterface $passwordEncoder;
	
	/**
	 * PasswordHashSubscriber constructor.
	 * @param  UserPasswordEncoderInterface  $passwordEncoder
	 */
	public function __construct(UserPasswordEncoderInterface $passwordEncoder)
	{
		$this->passwordEncoder = $passwordEncoder;
	}
	
	/**
	 * @return array
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			PasswordHashEvent::class => ['hashPassword', 999],
		];
	}
	
	/**
	 * @param  PasswordHashEvent  $event
	 */
	public function hashPassword(PasswordHashEvent $event): void
	{
		$user = $event->getUser();
		
		if ($user->getPlainPassword() !== $user->getRetypedPlainPassword()) {
			
			throw new ValidatorException('Passwords do not match!');
		}
		
		$user->setPassword(
			$this->passwordEncoder->encodePassword(
				$user,
				$user->getPlainPassword()
			)
		);
		$user->setPasswordResetToken(null);
		$user->setForcePasswordChange(false);
		$user->eraseCredentials();
	}
}