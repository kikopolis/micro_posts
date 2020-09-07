<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\User;

use App\Entity\User;
use App\Entity\UserPreferences;
use App\EventSubscriber\User\LocaleSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * @covers \App\EventSubscriber\User\LocaleSubscriber
 * Class LocaleSubscriberTest
 * @package App\Tests\unit\EventSubscriber\User
 */
class LocaleSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = LocaleSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(SecurityEvents::INTERACTIVE_LOGIN, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testOnInteractiveLogin()
	{
		/** @var SessionInterface $session */
		$session = Stub::makeEmpty(
			SessionInterface::class,
			[
				'set' => Stub\Expected::once(),
			],
			$this
		);
		
		/** @var UserPreferences $preferences */
		$preferences = Stub::make(
			UserPreferences::class,
			[
				'getLocale' => Stub\Expected::once('en'),
			],
			$this
		);
		
		/** @var User $user */
		$user = Stub::make(
			User::class,
			[
				'getPreferences' => Stub\Expected::once(
					$preferences
				),
			],
			$this
		);
		
		/** @var InteractiveLoginEvent $event */
		$event = Stub::make(
			InteractiveLoginEvent::class,
			[
				'getAuthenticationToken' => Stub\Expected::once(
					Stub::makeEmpty(
						TokenInterface::class,
						[
							'getUser' => $user,
						],
						$this
					)
				),
			],
			$this
		);
		
		$subscriber = new LocaleSubscriber($session);
		
		$subscriber->onInteractiveLogin($event);
	}
}