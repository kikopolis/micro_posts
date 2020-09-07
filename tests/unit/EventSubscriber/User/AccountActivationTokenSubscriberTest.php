<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\User;

use App\Entity\User;
use App\Event\User\AccountActivationTokenEvent;
use App\EventSubscriber\User\AccountActivationTokenSubscriber;
use App\Service\Contracts\MailerContract;
use App\Service\Contracts\TokenGeneratorContract;
use App\Service\Mailer;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @covers \App\EventSubscriber\User\AccountActivationTokenSubscriber
 * Class AccountActivationTokenSubscriberTest
 * @package App\Tests\unit\EventSubscriber\User
 */
class AccountActivationTokenSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = AccountActivationTokenSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(AccountActivationTokenEvent::class, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testToken()
	{
		/** @var MailerContract $mailer */
		$mailer = Stub::makeEmpty(MailerContract::class);
		
		/** @var User $user */
		$user = Stub::make(
			User::class,
			[
				'setConfirmationToken' => Stub\Expected::once(
					Stub::makeEmpty(User::class)
				),
			],
			$this
		);
		
		/** @var AccountActivationTokenEvent $event */
		$event = $this->getEvent($user);
		
		$subscriber = new AccountActivationTokenSubscriber(
			$this->getTokenGen(),
			$mailer
		);
		
		$subscriber->token($event);
	}
	
	/**
	 * @throws Exception
	 */
	public function testSendToken()
	{
		/** @var Mailer $mailer */
		$mailer = Stub::make(
			Mailer::class,
			[
				'activationToken' => Stub\Expected::once(),
			],
			$this
		);
		
		/** @var Mailer $user */
		$user = Stub::makeEmpty(User::class);
		
		/** @var AccountActivationTokenEvent $event */
		$event = $this->getEvent($user);
		
		$subscriber = new AccountActivationTokenSubscriber(
			$this->getTokenGen(),
			$mailer
		);
		
		$subscriber->sendToken($event);
	}
	
	/**
	 * @param $user
	 * @return object|MockObject|AccountActivationTokenEvent
	 * @throws Exception
	 */
	private function getEvent($user): AccountActivationTokenEvent
	{
		return Stub::make(
			AccountActivationTokenEvent::class,
			[
				'getUser' => Stub\Expected::once($user),
			],
			$this
		);
	}
	
	/**
	 * @return object|MockObject|TokenGeneratorContract
	 * @throws Exception
	 */
	private function getTokenGen(): TokenGeneratorContract
	{
		return Stub::makeEmpty(
			TokenGeneratorContract::class,
			[
				'generate' => 'token1234',
			],
			$this
		);
	}
}