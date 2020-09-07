<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber;

use App\Entity\Contracts\AuthorableContract;
use App\Entity\User;
use App\Event\AuthorableCreatedEvent;
use App\EventSubscriber\AuthorableCreatedSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;

/**
 * @covers \App\EventSubscriber\AuthorableCreatedSubscriber
 * Class AuthorableCreatedSubscriberTest
 * @package App\Tests\unit\EventSubscriber
 */
class AuthorableCreatedSubscriberTest extends Unit
{
	public function testGetSubscriberEvents()
	{
		$result = AuthorableCreatedSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(AuthorableCreatedEvent::class, $result);
	}
	
	/**
	 * @throws \Exception
	 */
	public function testSetAuthor()
	{
		/** @var User $user */
		$user = Stub::make(User::class);
		
		/** @var AuthorableContract $authorable */
		$authorable = Stub::makeEmpty(
			AuthorableContract::class,
			[
				'setAuthor',
				'getAuthor' => Stub\Expected::once(null),
			],
			$this
		);
		
		/** @var AuthorableCreatedEvent $event */
		$event = Stub::make(
			AuthorableCreatedEvent::class,
			[
				'getAuthorable' => Stub\Expected::once($authorable),
				'getAuthor'     => Stub\Expected::once($user),
			]
			, $this
		);
		
		$subscriber = new AuthorableCreatedSubscriber();
		
		$subscriber->setAuthor($event);
	}
}