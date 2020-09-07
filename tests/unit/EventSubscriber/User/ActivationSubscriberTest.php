<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\User;

use App\Entity\User;
use App\Event\User\ActivationEvent;
use App\EventSubscriber\User\ActivationSubscriber;
use App\Service\Contracts\MailerContract;
use App\Service\Mailer;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;

/**
 * @covers \App\EventSubscriber\User\ActivationSubscriber
 * Class ActivationSubscriberTest
 * @package App\Tests\unit\EventSubscriber\User
 */
class ActivationSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = ActivationSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(ActivationEvent::class, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testActivate()
	{
		/** @var User $emptyUser */
		$emptyUser = Stub::make(User::class);
		
		/** @var User $user */
		$user = Stub::make(
			User::class,
			[
				'setConfirmationToken' => Stub\Expected::once(
					$emptyUser
				),
				'activate'             => Stub\Expected::once(
					$emptyUser
				),
			],
			$this
		);
		
		/** @var ActivationEvent $event */
		$event = Stub::make(
			ActivationEvent::class,
			[
				'getUser' => Stub\Expected::once(
					$user
				),
			],
			$this
		);
		
		/** @var MailerContract $mailer */
		$mailer = Stub::makeEmpty(MailerContract::class);
		
		$subscriber = new ActivationSubscriber($mailer);
		
		$subscriber->activate($event);
	}
	
	/**
	 * @throws Exception
	 */
	public function testSuccessEmail()
	{
		/** @var ActivationEvent $event */
		$event = Stub::make(
			ActivationEvent::class,
			[
				'getUser' => Stub\Expected::once(
					Stub::make(User::class)
				),
			],
			$this
		);
		
		/** @var Mailer $mailer */
		$mailer = Stub::make(
			Mailer::class,
			[
				'activationSuccess' => Stub\Expected::once(),
			],
			$this
		);
		
		$subscriber = new ActivationSubscriber($mailer);
		
		$subscriber->successEmail($event);
	}
}