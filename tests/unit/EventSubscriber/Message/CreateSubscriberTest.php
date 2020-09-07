<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Message;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Event\Message\CreateEvent;
use App\EventSubscriber\Message\CreateSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * @covers \App\EventSubscriber\Message\CreateSubscriber
 * Class CreateSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Message
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
	public function testNotify()
	{
		/** @var Conversation $conversation */
		$conversation = Stub::make(Conversation::class);
		
		/** @var Message $message */
		$message = Stub::make(
			Message::class,
			[
				'getAuthor' => Stub::make(User::class),
			],
			$this
		);
		
		/** @var CreateEvent $event */
		$event = Stub::make(
			CreateEvent::class,
			[
				'getMessage'      => Stub\Expected::once($message),
				'getConversation' => Stub\Expected::once($conversation),
				'getParticipants' => Stub\Expected::once(
					[
						Stub::make(User::class),
						Stub::make(User::class),
						Stub::make(User::class),
					]
				),
			],
			$this
		);
		
		/** @var EntityManagerInterface $em */
		$em = Stub::makeEmpty(
			EntityManagerInterface::class,
			[
				'persist' => Stub\Expected::exactly(3),
			]
			,
			$this
		);
		
		$subscriber = new CreateSubscriber($em);
		
		$subscriber->notify($event);
	}
}