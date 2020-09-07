<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber\Comment;

use App\Entity\Comment;
use App\Entity\User;
use App\Event\Comment\UnLikeEvent;
use App\EventSubscriber\Comment\UnLikeSubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

/**
 * @covers  \App\EventSubscriber\Comment\UnLikeSubscriber
 * Class UnLikeSubscriberTest
 * @package App\Tests\unit\EventSubscriber\Comment
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
	public function testUnlike()
	{
		/** @var Comment $comment */
		$comment = Stub::make(
			Comment::class,
			[
				'unlike'     => Stub\Expected::once(
					Stub::make(Comment::class)
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
				'getComment'   => Stub\Expected::once($comment),
				'getUnlikedBy' => Stub\Expected::once($user),
			],
			$this
		);
		
		$subscriber = new UnLikeSubscriber();
		
		$subscriber->unlike($event);
	}
}