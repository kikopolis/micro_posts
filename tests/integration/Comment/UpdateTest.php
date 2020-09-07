<?php

declare(strict_types = 1);

namespace App\Tests\integration\Comment;

use App\Entity\Comment;
use App\Entity\Notification\UserMentionedInCommentNotification;
use App\Entity\User;
use App\Event\Comment\MentionedInCommentEvent;
use App\Event\Comment\UpdateEvent;
use App\Event\TimeStampableUpdatedEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use App\Tests\IntegrationTester;
use Codeception\Test\Unit;

/**
 * @coversNothing
 * Class UpdateTest
 * @package App\Tests\integration\Comment
 */
class UpdateTest extends Unit
{
	use EventDispatcherConcern;
	use EntityManagerConcern;
	
	/**
	 * @var IntegrationTester
	 */
	protected IntegrationTester $tester;
	
	public function testUpdate()
	{
		$ed = $this->getEd();
		$em = $this->getEm();
		
		$comment = $em->find(Comment::class, 1);
		
		$author = $comment->getAuthor();
		
		$oldBody = $comment->getBody();
		
		$newBody = 'this is a valid new comment body. this should get saved!!!!';
		
		$comment->setBody($newBody);
		
		$user1 = $em->find(User::class, 2);
		
		$user2 = $em->find(User::class, 3);
		
		$ed->dispatch(
			new UpdateEvent($comment)
		);
		
		$ed->dispatch(
			new TimeStampableUpdatedEvent($comment)
		);
		
		$ed->dispatch(
			new MentionedInCommentEvent(
				[$user1, $user2],
				$comment
			)
		);
		
		$em->flush();
		
		// assertions
		$this->tester->canSeeInRepository(
			Comment::class,
			['body' => $newBody]
		);
		
		$this->tester->cantSeeInRepository(
			Comment::class,
			['body' => $oldBody]
		);
		
		self::assertNotTrue($comment->isApproved());
		self::assertNotNull($comment->getCreatedAt());
		self::assertNotNull($comment->getUpdatedAt());
		
		self::assertEquals($newBody, $comment->getBody());
		self::assertNotEquals($oldBody, $comment->getBody());
		
		self::assertEquals($author->getId(), $comment->getAuthor()->getId());
		
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