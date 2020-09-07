<?php

declare(strict_types = 1);

namespace App\Tests\integration\Post;

use App\Entity\Comment;
use App\Entity\Notification\CommentPostedNotification;
use App\Entity\Post;
use App\Entity\User;
use App\Event\AuthorableCreatedEvent;
use App\Event\Comment\CreateEvent;
use App\Event\Comment\DeleteEvent;
use App\Event\TimeStampableCreatedEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use App\Tests\IntegrationTester;
use Codeception\Test\Unit;

/**
 * @coversNothing
 * Class DeleteCommentPostStays
 * @package App\Tests\integration\Post
 */
class DeleteCommentPostStaysTest extends Unit
{
	use EventDispatcherConcern;
	use EntityManagerConcern;
	
	/**
	 * @var IntegrationTester
	 */
	protected IntegrationTester $tester;
	
	public function testDeleteCommentPostStays()
	{
		$ed = $this->getEd();
		$em = $this->getEm();
		
		$body = 'this is a valid comment body. This should be saved!';
		
		$post = $em->find(Post::class, 1);
		
		$author = $em->find(User::class, 1);
		
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
		
		$em->persist($comment);
		$em->flush();
		
		// assertions - assert we have a successful comment
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
		
		$ed->dispatch(
			new DeleteEvent($post->getAuthor(), $comment)
		);
		
		$em->flush();
		
		// assertions
		$this->tester->canSeeInRepository(
			Post::class,
			[
				'id' => 1,
			]
		);
		
		$this->tester->cantSeeInRepository(
			Comment::class,
			[
				'body' => $body,
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