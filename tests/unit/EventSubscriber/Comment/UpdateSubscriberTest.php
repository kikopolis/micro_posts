<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Comment;

use App\Entity\Comment;
use App\Event\Comment\UpdateEvent;
use App\EventSubscriber\Comment\UpdateSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;

/**
 * @covers \App\EventSubscriber\Comment\UpdateSubscriber
 * Class UpdateSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Comment
 */
class UpdateSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = UpdateSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(UpdateEvent::class, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testUnApprove()
	{
		/** @var Comment $comment */
		$comment = Stub::make(
			Comment::class,
			[
				'unApprove' => Stub\Expected::once(
					Stub::make(Comment::class)
				),
			],
			$this
		);
		
		/** @var UpdateEvent $event */
		$event = Stub::make(
			UpdateEvent::class,
			[
				'getComment' => Stub\Expected::once($comment),
			],
			$this
		);
		
		$subscriber = new UpdateSubscriber();
		
		$subscriber->unApprove($event);
	}
}