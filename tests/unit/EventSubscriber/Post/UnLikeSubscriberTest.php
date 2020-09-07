<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Post;

use App\Entity\Post;
use App\Entity\User;
use App\Event\Post\UnLikeEvent;
use App\EventSubscriber\Post\UnLikeSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

/**
 * @covers  \App\EventSubscriber\Post\UnLikeSubscriber
 * Class UnLikeSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Post
 */
class UnLikeSubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = UnLikeSubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(UnLikeEvent::class, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testUnLike()
	{
		/** @var Post $post */
		$post = Stub::make(
			Post::class,
			[
				'unlike'     => Stub\Expected::once(
					Stub::make(Post::class)
				),
				'getAuthor'  => Stub\Expected::once(
					Stub::make(
						User::class,
						[
							'getId' => Stub\Expected::once(1),
						],
						$this
					)
				),
				'getLikedBy' => Stub\Expected::once(
					Stub::make(
						ArrayCollection::class,
						[
							'contains' => Stub\Expected::once(true),
						],
						$this
					)
				),
			],
			$this
		);
		
		/** @var User $user */
		$user = Stub::make(
			User::class,
			[
				'getId' => Stub\Expected::once(2),
			],
			$this
		);
		
		/** @var UnLikeEvent $event */
		$event = Stub::make(
			UnLikeEvent::class,
			[
				'getPost'      => Stub\Expected::once($post),
				'getUnlikedBy' => Stub\Expected::once($user),
			],
			$this
		);
		
		$subscriber = new UnLikeSubscriber();
		
		$subscriber->unLike($event);
	}
}