<?php

declare(strict_types = 1);

namespace App\Tests\integration\Post;

use App\Entity\Notification\FollowedUserPostsNotification;
use App\Entity\Notification\UserMentionedInPostNotification;
use App\Entity\Post;
use App\Entity\User;
use App\Event\AuthorableCreatedEvent;
use App\Event\Post\CreateEvent;
use App\Event\Post\FollowedUserPostsEvent;
use App\Event\Post\MentionedInPostEvent;
use App\Event\TimeStampableCreatedEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use App\Tests\IntegrationTester;
use Codeception\Test\Unit;

/**
 * @coversNothing
 * Class CreateTest
 * @package App\Tests\integration\Post
 */
class CreateTest extends Unit
{
	use EventDispatcherConcern;
	use EntityManagerConcern;
	
	/**
	 * @var IntegrationTester
	 */
	protected IntegrationTester $tester;
	
	public function testCreate()
	{
		$ed = $this->getEd();
		$em = $this->getEm();
		
		$author = $em->find(User::class, 1);
		
		$body = 'this is a legal post body. this should be saved.';
		
		$post = new Post(
			$body
		);
		
		$user1 = $em->find(User::class, 2);
		
		$user2 = $em->find(User::class, 3);
		
		$ed->dispatch(
			new AuthorableCreatedEvent($post, $author)
		);
		
		$ed->dispatch(
			new CreateEvent($post)
		);
		
		$ed->dispatch(
			new TimeStampableCreatedEvent($post)
		);
		
		$ed->dispatch(
			new FollowedUserPostsEvent(
				[$user1, $user2],
				$post,
				$post->getAuthor()
			)
		);
		
		$ed->dispatch(
			new MentionedInPostEvent(
				[$user1, $user2],
				$post
			)
		);
		
		$em->persist($post);
		$em->flush();
		
		// assertions
		$this->tester->canSeeInRepository(Post::class, ['body' => $body]);
		
		static::assertTrue($post->isPublished());
		static::assertNotTrue($post->isReported());
		static::assertNotTrue($post->isTrashed());
		static::assertNotTrue($post->isApproved());
		
		static::assertNotNull($post->getCreatedAt());
		static::assertNull($post->getUpdatedAt());
		
		static::assertEquals($author->getId(), $post->getAuthor()->getId());
		
		static::assertEquals(0, $post->getComments()->count());
		static::assertEquals(0, $post->getLikedBy()->count());
		
		$this->tester->canSeeInRepository(
			FollowedUserPostsNotification::class,
			[
				'owner' => $user1,
			]
		);
		
		$this->tester->canSeeInRepository(
			FollowedUserPostsNotification::class,
			[
				'owner' => $user2,
			]
		);
		
		$this->tester->canSeeInRepository(
			UserMentionedInPostNotification::class,
			[
				'owner' => $user1,
			]
		);
		
		$this->tester->canSeeInRepository(
			UserMentionedInPostNotification::class,
			[
				'owner' => $user2,
			]
		);
	}
}