<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\User;

use App\Entity\User;
use App\Event\User\RestoreEvent;
use App\EventSubscriber\User\RestoreSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;

/**
 * @covers  \App\EventSubscriber\User\RestoreSubscriber
 * Class RestoreSubscriberTest
 * @package App\Tests\unit\EventSubscriber\User
 */
class RestoreSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = RestoreSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(
			RestoreEvent::class,
			$result
		);
	}
	
	/**
	 * @throws Exception
	 */
	public function testRestore()
	{
		$stubUser = Stub::make(User::class);
		
		/** @var User $user */
		$user = Stub::make(
			User::class,
			[
				'restore'       => Stub\Expected::once($stubUser),
				'setRestoredBy' => Stub\Expected::once($stubUser),
			],
			$this
		);
		
		/** @var RestoreEvent $event */
		$event = Stub::make(
			RestoreEvent::class,
			[
				'getRestored'   => Stub\Expected::once($user),
				'getRestoredBy' => Stub\Expected::once($stubUser),
			],
			$this
		);
		
		$subscriber = new RestoreSubscriber();
		
		$subscriber->restore($event);
	}
}