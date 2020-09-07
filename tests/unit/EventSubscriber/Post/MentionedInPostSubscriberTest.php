<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Post;

use App\Entity\Post;
use App\Entity\User;
use App\Event\Post\MentionedInPostEvent;
use App\Event\TimeStampableCreatedEvent;
use App\EventSubscriber\Post\MentionedInPostSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @covers \App\EventSubscriber\Post\MentionedInPostSubscriber
 * Class MentionedInPostSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Post
 */
class MentionedInPostSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = MentionedInPostSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(MentionedInPostEvent::class, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testNotify()
	{
		/** @var EntityManagerInterface $em */
		$em = Stub::makeEmpty(
			EntityManagerInterface::class,
			[
				'persist' => Stub\Expected::exactly(3),
			],
			$this
		);
		
		/** @var User $user */
		$user = Stub::make(User::class);
		
		/** @var Post $post */
		$post = Stub::make(
			Post::class,
			[
				'getAuthor' => Stub\Expected::once(
					Stub::make(User::class)
				),
			],
			$this
		);
		
		/** @var MentionedInPostEvent $event */
		$event = Stub::make(
			MentionedInPostEvent::class,
			[
				'getTaggedUsers' => Stub\Expected::once(
					[
						$user,
						$user,
						$user,
					]
				),
				'getPost'        => Stub\Expected::once($post),
			],
			$this
		);
		
		/** @var TimeStampableCreatedEvent $secondEvent */
		$secondEvent = Stub::make(TimeStampableCreatedEvent::class);
		
		/** @var EventDispatcherInterface $ed */
		$ed = Stub::makeEmpty(
			EventDispatcherInterface::class,
			[
				'dispatch' => Stub\Expected::exactly(3, $secondEvent),
			],
			$this
		);
		
		$subscriber = new MentionedInPostSubscriber($em, $ed);
		
		$subscriber->notify($event);
	}
}