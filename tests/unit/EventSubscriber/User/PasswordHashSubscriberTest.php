<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\User;

use App\Entity\User;
use App\Event\User\PasswordHashEvent;
use App\EventSubscriber\User\PasswordHashSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @covers \App\EventSubscriber\User\PasswordHashSubscriber
 * Class PasswordHashSubscriberTest
 * @package App\Tests\unit\EventSubscriber\User
 */
class PasswordHashSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = PasswordHashSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(PasswordHashEvent::class, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testHashPassword()
	{
		/** @var User $emptyUser */
		$emptyUser = Stub::make(User::class);
		
		/** @var User $user */
		$user = Stub::make(
			User::class,
			[
				'getPlainPassword'        => Stub\Expected::exactly(2, 'password'),
				'getRetypedPlainPassword' => Stub\Expected::once('password'),
				'setPassword'             => Stub\Expected::once($emptyUser),
				'eraseCredentials'        => Stub\Expected::once($emptyUser),
				'setForcePasswordChange'  => Stub\Expected::once($emptyUser),
				'setPasswordResetToken'   => Stub\Expected::once($emptyUser),
			],
			$this
		);
		
		/** @var PasswordHashEvent $event */
		$event = Stub::make(
			PasswordHashEvent::class,
			[
				'getUser' => $user,
			],
			$this
		);
		
		/** @var UserPasswordEncoderInterface $encoder */
		$encoder = Stub::makeEmpty(
			UserPasswordEncoderInterface::class,
			[
				'encodePassword' => Stub\Expected::once('########'),
			],
			$this
		);
		
		$subscriber = new PasswordHashSubscriber($encoder);
		
		$subscriber->hashPassword($event);
	}
}