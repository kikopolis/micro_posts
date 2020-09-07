<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Post;

use App\Entity\Post;
use App\Event\Post\ViewEvent;
use App\EventSubscriber\Post\ViewSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;

/**
 * @covers \App\EventSubscriber\Post\ViewSubscriber
 * Class ViewSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Post
 */
class ViewSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = ViewSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(ViewEvent::class, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testIncrement()
	{
		/** @var Post $post */
		$post = Stub::make(
			Post::class,
			[
				'incrementViewCounters' => Stub\Expected::once(),
			],
			$this
		);
		
		/** @var ViewEvent $event */
		$event = Stub::make(
			ViewEvent::class,
			[
				'getPost' => Stub\Expected::once($post),
			],
			$this
		);
		
		$subscriber = new ViewSubscriber();
		
		$subscriber->increment($event);
	}
}