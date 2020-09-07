<?php

declare(strict_types = 1);

namespace App\Tests\integration\Comment;

use App\Entity\Comment;
use App\Entity\Notification\CommentPostedNotification;
use App\Entity\Notification\FollowedUserCommentsNotification;
use App\Entity\Notification\UserMentionedInCommentNotification;
use App\Entity\Post;
use App\Entity\User;
use App\Event\AuthorableCreatedEvent;
use App\Event\Comment\CreateEvent;
use App\Event\Comment\FollowedUserCommentsEvent;
use App\Event\Comment\MentionedInCommentEvent;
use App\Event\TimeStampableCreatedEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use App\Tests\IntegrationTester;
use Codeception\Test\Unit;

/**
 * @coversNothing
 * Class CreateTest
 * @package App\Tests\integration\Comment
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
		
		$body = 'this is a valid comment body. This should be saved!';
		
		$post = $em->find(Post::class, 1);
		
		$author = $em->find(User::class, 1);
		
		$user1 = $em->find(User::class, 2);
		
		$user2 = $em->find(User::class, 3);
		
		$comment = new Comment($body);
		
		$comment->setPost($post);
		
		$ed->dispatch(
			new AuthorableCreatedEvent($comment, $author)
		);
		
		$ed->dispatch(
			new CreateEvent($comment)
		);
		
		$ed->dispatch(
			new TimeStampableCreatedEvent($comment)
		);
		
		$ed->dispatch(
			new FollowedUserCommentsEvent(
				[$user1, $user2],
				$comment
			)
		);
		
		$ed->dispatch(
			new MentionedInCommentEvent(
				[$user1, $user2],
				$comment
			)
		);
		
		$em->persist($comment);
		$em->flush();
		
		// assertions
		$this->tester->canSeeInRepository(
			Comment::class,
			[
				'body' => $body,
			]
		);
		
		$this->tester->canSeeInRepository(
			CommentPostedNotification::class,
			[
				'owner' => $post->getAuthor(),
			]
		);
		
		static::assertNotTrue($comment->isReported());
		static::assertNotTrue($comment->isTrashed());
		static::assertNotTrue($comment->isApproved());
		
		static::assertNotNull($comment->getCreatedAt());
		static::assertNull($comment->getUpdatedAt());
		
		static::assertEquals($author->getId(), $comment->getAuthor()->getId());
		static::assertEquals(0, $comment->getLikedBy()->count());
		
		$this->tester->canSeeInRepository(
			FollowedUserCommentsNotification::class,
			[
				'owner' => $user1,
			]
		);
		
		$this->tester->canSeeInRepository(
			FollowedUserCommentsNotification::class,
			[
				'owner' => $user2,
			]
		);
		
		$this->tester->canSeeInRepository(
			UserMentionedInCommentNotification::class,
			[
				'owner' => $user1,
			]
		);
		
		$this->tester->canSeeInRepository(
			UserMentionedInCommentNotification::class,
			[
				'owner' => $user2,
			]
		);
		
	}
}