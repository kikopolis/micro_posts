<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\User;

use App\Entity\User;
use App\Event\User\PasswordSecurityEvent;
use App\EventSubscriber\User\PasswordSecuritySubscriber;
use App\Service\Mailer;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;

/**
 * @covers \App\EventSubscriber\User\PasswordSecuritySubscriber
 * Class PasswordSecuritySubscriberTest
 * @package App\Tests\unit\EventSubscriber\User
 */
class PasswordSecuritySubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = PasswordSecuritySubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(PasswordSecurityEvent::class, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testNotify()
	{
		/** @var PasswordSecurityEvent $event */
		$event = Stub::make(
			PasswordSecurityEvent::class,
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
				'passwordSecurity' => Stub\Expected::once(),
			],
			$this
		);
		
		$subscriber = new PasswordSecuritySubscriber($mailer);
		
		$subscriber->notify($event);
	}
}