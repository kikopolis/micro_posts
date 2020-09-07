<?php

declare(strict_types = 1);

namespace App\Tests\integration\User;

use App\Entity\Comment;
use App\Entity\Notification\CommentPostedNotification;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\UserPreferences;
use App\Entity\UserProfile;
use App\Event\AuthorableCreatedEvent;
use App\Event\Notification\DeleteEvent;
use App\Event\TimeStampableCreatedEvent;
use App\Event\User\CreateEvent;
use App\Event\User\PasswordHashEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use App\Tests\IntegrationTester;
use Codeception\Test\Unit;
use DateTime;

/**
 * @coversNothing
 * Class DeleteNotificationUserStaysTest
 * @package App\Tests\integration\User
 */
class DeleteNotificationUserStaysTest extends Unit
{
	use EventDispatcherConcern;
	use EntityManagerConcern;
	
	/**
	 * @var IntegrationTester
	 */
	protected IntegrationTester $tester;
	
	public function testDeleteNotificationUserStays()
	{
		$ed = $this->getEd();
		$em = $this->getEm();
		
		$user = $em->find(User::class, 1);
		
		$body = 'this is a valid comment body. This should be saved!';
		
		/** @var Post $post */
		$post = $em->find(Post::class, 1);
		
		$comment = new Comment($body);
		
		$comment->setPost($post);
		
		$ed->dispatch(
			new AuthorableCreatedEvent($comment, $user)
		);
		
		$ed->dispatch(
			new \App\Event\Comment\CreateEvent($comment)
		);
		
		$ed->dispatch(
			new TimeStampableCreatedEvent($comment)
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
		
		/** @var CommentPostedNotification $note */
		$note = $this->tester->grabEntityFromRepository(
			CommentPostedNotification::class,
			[
				'owner' => $post->getAuthor(),
			]
		);
		
		$ed->dispatch(
			new DeleteEvent($note)
		);
		
		$em->flush();
		
		// assertions
		$this->tester->canSeeInRepository(
			Post::class,
			[
				'id' => $post->getId(),
			]
		);
		
		$this->tester->canSeeInRepository(
			Comment::class,
			[
				'body' => $body,
			]
		);
		
		$this->tester->canSeeInRepository(
			User::class,
			[
				'id' => $post->getAuthor()->getId(),
			]
		);
		
		$this->tester->canSeeInRepository(
			User::class,
			[
				'id' => $comment->getAuthor()->getId(),
			]
		);
		
		$this->tester->cantSeeInRepository(
			CommentPostedNotification::class,
			[
				'owner' => $post->getAuthor(),
			]
		);
	}
}