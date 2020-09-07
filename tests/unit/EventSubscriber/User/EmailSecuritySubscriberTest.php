<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\User;

use App\Entity\User;
use App\Event\User\EmailSecurityEvent;
use App\EventSubscriber\User\EmailSecuritySubscriber;
use App\Service\Mailer;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;

/**
 * @covers \App\EventSubscriber\User\EmailSecuritySubscriber
 * Class EmailSecuritySubscriberTest
 * @package App\Tests\unit\EventSubscriber\User
 */
class EmailSecuritySubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = EmailSecuritySubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(EmailSecurityEvent::class, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testNotify()
	{
		/** @var Mailer $mailer */
		$mailer = Stub::make(
			Mailer::class,
			[
				'emailSecurity' => Stub\Expected::once(),
			],
			$this
		);
		
		/** @var User $user */
		$user = Stub::make(
			User::class,
			[],
			$this
		);
		
		/** @var EmailSecurityEvent $event */
		$event = Stub::make(
			EmailSecurityEvent::class,
			[
				'getUser' => Stub\Expected::once($user),
			],
			$this
		);
		
		$subscriber = new EmailSecuritySubscriber($mailer);
		
		$subscriber->notify($event);
	}
}