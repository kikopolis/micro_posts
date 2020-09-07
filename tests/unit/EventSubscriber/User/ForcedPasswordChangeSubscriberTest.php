<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\User;

use App\Entity\User;
use App\Event\User\ForcedPasswordChangeEvent;
use App\EventSubscriber\User\ForcedPasswordChangeSubscriber;
use App\Service\Contracts\MailerContract;
use App\Service\Contracts\TokenGeneratorContract;
use App\Service\Mailer;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;

/**
 * @covers \App\EventSubscriber\User\ForcedPasswordChangeSubscriber
 * Class ForcedPasswordChangeSubscriberTest
 * @package App\Tests\unit\EventSubscriber\User
 */
class ForcedPasswordChangeSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = ForcedPasswordChangeSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(ForcedPasswordChangeEvent::class, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testToken()
	{
		/** @var TokenGeneratorContract $tokenGen */
		$tokenGen = Stub::makeEmpty(
			TokenGeneratorContract::class,
			[
				'generate' => Stub\Expected::once('token1234'),
			],
			$this
		);
		
		/** @var MailerContract $mailer */
		$mailer = Stub::makeEmpty(MailerContract::class);
		
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
		
		/** @var ForcedPasswordChangeEvent $event */
		$event = Stub::make(
			ForcedPasswordChangeEvent::class,
			[
				'getUser' => Stub\Expected::once($user),
			],
			$this
		);
		
		$subscriber = new ForcedPasswordChangeSubscriber(
			$tokenGen,
			$mailer
		);
		
		$subscriber->token($event);
	}
	
	/**
	 * @throws Exception
	 */
	public function testSendToken()
	{
		/** @var TokenGeneratorContract $tokenGen */
		$tokenGen = Stub::makeEmpty(TokenGeneratorContract::class);
		
		/** @var Mailer $mailer */
		$mailer = Stub::makeEmpty(
			Mailer::class,
			[
				'forcedPasswordToken' => Stub\Expected::once(),
			],
			$this
		);
		
		/** @var User $user */
		$user = Stub::make(User::class);
		
		/** @var ForcedPasswordChangeEvent $event */
		$event = Stub::make(
			ForcedPasswordChangeEvent::class,
			[
				'getUser' => Stub\Expected::once($user),
			],
			$this
		);
		
		$subscriber = new ForcedPasswordChangeSubscriber(
			$tokenGen,
			$mailer
		);
		
		$subscriber->sendToken($event);
	}
}