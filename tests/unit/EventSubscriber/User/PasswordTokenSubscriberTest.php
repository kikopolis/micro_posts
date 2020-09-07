<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\User;

use App\Entity\User;
use App\Event\User\PasswordTokenEvent;
use App\EventSubscriber\User\PasswordTokenSubscriber;
use App\Service\Contracts\MailerContract;
use App\Service\Contracts\TokenGeneratorContract;
use App\Service\Mailer;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;

/**
 * @covers \App\EventSubscriber\User\PasswordTokenSubscriber
 * Class PasswordTokenSubscriberTest
 * @package App\Tests\unit\EventSubscriber\User
 */
class PasswordTokenSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = PasswordTokenSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(PasswordTokenEvent::class, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testToken()
	{
		/** @var User $user */
		$user = Stub::make(
			User::class,
			[
				'setPasswordResetToken' => Stub\Expected::once(
					Stub::make(User::class)
				),
			],
			$this
		);
		
		/** @var PasswordTokenEvent $event */
		$event = Stub::make(
			PasswordTokenEvent::class,
			[
				'getUser' => Stub\Expected::once($user),
			],
			$this
		);
		
		/** @var TokenGeneratorContract $tokenGenerator */
		$tokenGenerator = Stub::makeEmpty(
			TokenGeneratorContract::class,
			[
				'generate' => Stub\Expected::once('token1234'),
			],
			$this
		);
		
		/** @var MailerContract $mailer */
		$mailer = Stub::makeEmpty(MailerContract::class);
		
		$subscriber = new PasswordTokenSubscriber(
			$tokenGenerator,
			$mailer
		);
		
		$subscriber->token($event);
	}
	
	/**
	 * @throws Exception
	 */
	public function testTokenEmail()
	{
		/** @var User $user */
		$user = Stub::make(User::class);
		
		/** @var PasswordTokenEvent $event */
		$event = Stub::make(
			PasswordTokenEvent::class,
			[
				'getUser' => Stub\Expected::once($user),
			],
			$this
		);
		
		/** @var TokenGeneratorContract $tokenGenerator */
		$tokenGenerator = Stub::makeEmpty(TokenGeneratorContract::class);
		
		/** @var Mailer $mailer */
		$mailer = Stub::make(
			Mailer::class,
			[
				'passwordToken' => Stub\Expected::once(),
			],
			$this
		);
		
		$subscriber = new PasswordTokenSubscriber(
			$tokenGenerator,
			$mailer
		);
		
		$subscriber->tokenEmail($event);
	}
}