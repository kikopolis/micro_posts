<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\User;

use App\EventSubscriber\User\ForcedPasswordExceptionSubscriber;
use App\Security\Exception\ForcedPasswordChangeException;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

/**
 * @covers \App\EventSubscriber\User\ForcedPasswordExceptionSubscriber
 * Class ForcedPasswordExceptionSubscriberTest
 * @package App\Tests\unit\EventSubscriber\User
 */
class ForcedPasswordExceptionSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = ForcedPasswordExceptionSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(KernelEvents::EXCEPTION, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testForcedException()
	{
		/** @var ExceptionEvent $event */
		$event = Stub::make(
			ExceptionEvent::class,
			[
				'getThrowable' => Stub\Expected::once(
					Stub::make(ForcedPasswordChangeException::class)
				),
				'setResponse'  => Stub\Expected::once(),
			],
			$this
		);
		
		/** @var RouterInterface $router */
		$router = Stub::makeEmpty(
			RouterInterface::class,
			[
				'generate' => Stub\Expected::once('http://route.com'),
			],
			$this
		);
		
		$subscriber = new ForcedPasswordExceptionSubscriber($router);
		
		$subscriber->forcedException($event);
	}
}