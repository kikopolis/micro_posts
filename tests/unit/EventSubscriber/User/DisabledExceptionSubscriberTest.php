<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\User;

use App\EventSubscriber\User\DisabledExceptionSubscriber;
use App\Security\Exception\AccountDisabledException;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @covers \App\EventSubscriber\User\DisabledExceptionSubscriber
 * Class DisabledExceptionSubscriberTest
 * @package App\Tests\unit\EventSubscriber\User
 */
class DisabledExceptionSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = DisabledExceptionSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(KernelEvents::EXCEPTION, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testDisabled()
	{
		/** @var ExceptionEvent $event */
		$event = Stub::make(
			ExceptionEvent::class,
			[
				'getThrowable' => Stub\Expected::once(
					Stub::make(AccountDisabledException::class)
				),
			],
			$this
		);
		
		/** @var FlashBagInterface $flashBag */
		$flashBag = Stub::makeEmpty(
			FlashBagInterface::class,
			[
				'add' => Stub\Expected::once(),
			],
			$this
		);
		
		/** @var Session $session */
		$session = Stub::makeEmpty(
			Session::class,
			[
				'getFlashBag' => Stub\Expected::once(
					$flashBag
				),
			],
			$this
		);
		
		$subscriber = new DisabledExceptionSubscriber($session);
		
		$subscriber->disabled($event);
	}
}