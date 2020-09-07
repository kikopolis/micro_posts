<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Post;

use App\Entity\Post;
use App\Event\Post\UpdateEvent;
use App\EventSubscriber\Post\UpdateSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;

/**
 * @covers \App\EventSubscriber\Post\UpdateSubscriber
 * Class UpdateSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Post
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
		/** @var Post $post */
		$post = Stub::make(
			Post::class,
			[
				'unApprove' => Stub\Expected::once(
					Stub::make(Post::class)
				),
			],
			$this
		);
		
		/** @var UpdateEvent $event */
		$event = Stub::make(
			UpdateEvent::class,
			[
				'getPost' => Stub\Expected::once($post),
			],
			$this
		);
		
		$subscriber = new UpdateSubscriber();
		
		$subscriber->unApprove($event);
	}
}