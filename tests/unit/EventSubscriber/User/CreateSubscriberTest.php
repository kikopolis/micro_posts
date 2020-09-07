<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\User;

use App\Entity\User;
use App\Entity\UserPreferences;
use App\Entity\UserProfile;
use App\Event\User\CreateEvent;
use App\EventSubscriber\User\CreateSubscriber;
use App\Service\Contracts\MailerContract;
use App\Service\Contracts\TokenGeneratorContract;
use App\Service\Mailer;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * @covers  \App\EventSubscriber\User\CreateSubscriber
 * Class CreateSubscriberTest
 * @package App\Tests\unit\EventSubscriber\User
 */
class CreateSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = CreateSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(CreateEvent::class, $result);
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
				'setConfirmationToken' => Stub\Expected::once(
					Stub::make(User::class)
				),
			],
			$this
		);
		
		/** @var CreateEvent $event */
		$event = Stub::make(
			CreateEvent::class,
			[
				'getUser' => Stub\Expected::once($user),
			],
			$this
		);
		
		/** @var EntityManagerInterface $em */
		$em = Stub::makeEmpty(EntityManagerInterface::class);
		
		/** @var TokenGeneratorContract $tg */
		$tg = Stub::makeEmpty(
			TokenGeneratorContract::class,
			[
				'generate' => Stub\Expected::once('token1234'),
			],
			$this
		);
		
		/** @var MailerContract $mc */
		$mc = Stub::makeEmpty(MailerContract::class);
		
		/** @var UserProfile $prof */
		$prof = Stub::make(UserProfile::class);
		
		/** @var UserPreferences $pref */
		$pref = Stub::make(UserPreferences::class);
		
		$subscriber = new CreateSubscriber(
			$tg,
			$mc,
			$em,
			$prof,
			$pref
		);
		
		$subscriber->token($event);
	}
	
	/**
	 * @throws Exception
	 */
	public function testActivationEmail()
	{
		$user = Stub::make(User::class);
		
		/** @var CreateEvent $event */
		$event = Stub::make(
			CreateEvent::class,
			[
				'getUser' => Stub\Expected::once($user),
			],
			$this
		);
		
		/** @var EntityManagerInterface $em */
		$em = Stub::makeEmpty(EntityManagerInterface::class);
		
		/** @var TokenGeneratorContract $tg */
		$tg = Stub::makeEmpty(TokenGeneratorContract::class);
		
		/** @var MailerContract $mc */
		$mc = Stub::makeEmpty(
			Mailer::class,
			[
				'activationInfo' => Stub\Expected::once(),
			],
			$this
		);
		
		/** @var UserProfile $prof */
		$prof = Stub::make(UserProfile::class);
		
		/** @var UserPreferences $pref */
		$pref = Stub::make(UserPreferences::class);
		
		$subscriber = new CreateSubscriber(
			$tg,
			$mc,
			$em,
			$prof,
			$pref
		);
		
		$subscriber->activationEmail($event);
	}
	
	/**
	 * @throws Exception
	 */
	public function testProfileNotSet()
	{
		/** @var User $user */
		$user = Stub::make(
			User::class,
			[
				'getProfile' => Stub\Expected::once(null),
				
				'setProfile' => Stub\Expected::once(
					Stub::make(User::class)
				),
			],
			$this
		);
		
		/** @var CreateEvent $event */
		$event = Stub::make(
			CreateEvent::class,
			[
				'getUser' => Stub\Expected::once($user),
			],
			$this
		);
		
		/** @var EntityManagerInterface $em */
		$em = Stub::makeEmpty(EntityManagerInterface::class);
		
		/** @var TokenGeneratorContract $tg */
		$tg = Stub::makeEmpty(TokenGeneratorContract::class);
		
		/** @var MailerContract $mc */
		$mc = Stub::makeEmpty(MailerContract::class);
		
		/** @var UserProfile $prof */
		$prof = Stub::make(UserProfile::class);
		
		/** @var UserPreferences $pref */
		$pref = Stub::make(UserPreferences::class);
		
		$subscriber = new CreateSubscriber(
			$tg,
			$mc,
			$em,
			$prof,
			$pref
		);
		
		$subscriber->profile($event);
	}
	
	/**
	 * @throws Exception
	 */
	public function testProfileSet()
	{
		/** @var User $user */
		$user = Stub::make(
			User::class,
			[
				'getProfile' => Stub\Expected::once(
					Stub::make(UserProfile::class)
				),
				
				'setProfile' => Stub\Expected::never(),
			],
			$this
		);
		
		/** @var CreateEvent $event */
		$event = Stub::make(
			CreateEvent::class,
			[
				'getUser' => Stub\Expected::once($user),
			],
			$this
		);
		
		/** @var EntityManagerInterface $em */
		$em = Stub::makeEmpty(EntityManagerInterface::class);
		
		/** @var TokenGeneratorContract $tg */
		$tg = Stub::makeEmpty(TokenGeneratorContract::class);
		
		/** @var MailerContract $mc */
		$mc = Stub::makeEmpty(MailerContract::class);
		
		/** @var UserProfile $prof */
		$prof = Stub::make(UserProfile::class);
		
		/** @var UserPreferences $pref */
		$pref = Stub::make(UserPreferences::class);
		
		$subscriber = new CreateSubscriber(
			$tg,
			$mc,
			$em,
			$prof,
			$pref
		);
		
		$subscriber->profile($event);
	}
	
	/**
	 * @throws Exception
	 */
	public function testPreferencesNotSet()
	{
		$user = Stub::make(
			User::class,
			[
				'getPreferences' => Stub\Expected::once(null),
				
				'setPreferences' => Stub\Expected::once(
					Stub::make(User::class)
				),
			],
			$this
		);
		
		/** @var CreateEvent $event */
		$event = Stub::make(
			CreateEvent::class,
			[
				'getUser' => Stub\Expected::once($user),
			],
			$this
		);
		
		/** @var EntityManagerInterface $em */
		$em = Stub::makeEmpty(EntityManagerInterface::class);
		
		/** @var TokenGeneratorContract $tg */
		$tg = Stub::makeEmpty(TokenGeneratorContract::class);
		
		/** @var MailerContract $mc */
		$mc = Stub::makeEmpty(MailerContract::class);
		
		/** @var UserProfile $prof */
		$prof = Stub::make(UserProfile::class);
		
		/** @var UserPreferences $pref */
		$pref = Stub::make(UserPreferences::class);
		
		$subscriber = new CreateSubscriber(
			$tg,
			$mc,
			$em,
			$prof,
			$pref
		);
		
		$subscriber->preferences($event);
	}
	
	/**
	 * @throws Exception
	 */
	public function testPreferencesSet()
	{
		$user = Stub::make(
			User::class,
			[
				'getPreferences' => Stub\Expected::once(
					Stub::make(UserPreferences::class)
				),
				
				'setPreferences' => Stub\Expected::never(),
			],
			$this
		);
		
		/** @var CreateEvent $event */
		$event = Stub::make(
			CreateEvent::class,
			[
				'getUser' => Stub\Expected::once($user),
			],
			$this
		);
		
		/** @var EntityManagerInterface $em */
		$em = Stub::makeEmpty(EntityManagerInterface::class);
		
		/** @var TokenGeneratorContract $tg */
		$tg = Stub::makeEmpty(TokenGeneratorContract::class);
		
		/** @var MailerContract $mc */
		$mc = Stub::makeEmpty(MailerContract::class);
		
		/** @var UserProfile $prof */
		$prof = Stub::make(UserProfile::class);
		
		/** @var UserPreferences $pref */
		$pref = Stub::make(UserPreferences::class);
		
		$subscriber = new CreateSubscriber(
			$tg,
			$mc,
			$em,
			$prof,
			$pref
		);
		
		$subscriber->preferences($event);
	}
}