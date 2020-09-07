<?php

declare(strict_types = 1);

namespace App\Tests\integration\Post;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Event\AuthorableCreatedEvent;
use App\Event\Comment\CreateEvent;
use App\Event\Post\DeleteEvent;
use App\Event\TimeStampableCreatedEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use App\Tests\IntegrationTester;
use Codeception\Test\Unit;

/**
 * @coversNothing
 * Class DeleteWithCommentsTest
 * @package App\Tests\integration\Post
 */
class DeleteWithCommentsTest extends Unit
{
	use EventDispatcherConcern;
	use EntityManagerConcern;
	
	/**
	 * @var IntegrationTester
	 */
	protected IntegrationTester $tester;
	
	public function testDeleteWithComment()
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
		
		// assertions - assert we have a comment
		$this->tester->canSeeInRepository(
			Comment::class,
			[
				'body' => $body,
			]
		);
		
		$ed->dispatch(
			new DeleteEvent($post->getAuthor(), $post)
		);
		
		$em->flush();
		
		// assertions
		self::assertTrue(
			$em->find(User::class, 1) instanceof User
		);
		
		self::assertNull($em->find(Post::class, 1));
		
		$this->tester->cantSeeInRepository(
			Comment::class,
			[
				'body' => $body,
			]
		);
	}
}