<?php

declare(strict_types = 1);

namespace App\Tests\integration\Comment;

use App\Entity\Comment;
use App\Entity\Notification\CommentLikeNotification;
use App\Entity\User;
use App\Event\Comment\LikeEvent;
use App\Event\Comment\UnLikeEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use App\Tests\IntegrationTester;
use Codeception\Test\Unit;

/**
 * @coversNothing
 * Class UnLikeTest
 * @package App\Tests\integration\Comment
 */
class UnLikeTest extends Unit
{
	use EventDispatcherConcern;
	use EntityManagerConcern;
	
	/**
	 * @var IntegrationTester
	 */
	protected IntegrationTester $tester;
	
	public function testUnLike()
	{
		$ed = $this->getEd();
		$em = $this->getEm();
		
		/** @var Comment $comment */
		$comment = $em->find(Comment::class, 1);
		
		/** @var User $liker */
		$liker = $em->find(User::class, 1);
		
		self::assertNotEquals(
			$comment->getAuthor()->getId(),
			$liker->getId()
		);
		
		$likeCount = $comment->getLikeCount();
		
		$ed->dispatch(
			new LikeEvent($liker, $comment)
		);
		
		$em->flush();
		
		// assertions
		self::assertTrue($comment->getLikedBy()->contains($liker));
		self::assertTrue($likeCount < $comment->getLikeCount());
		
		$this->tester->canSeeInRepository(
			CommentLikeNotification::class,
			[
				'owner' => $comment->getAuthor(),
			]
		);
		
		// get the like count again to reflect the user having liked the comment
		$likeCount = $comment->getLikeCount();
		
		$ed->dispatch(
			new UnLikeEvent($liker, $comment)
		);
		
		$em->flush();
		
		self::assertFalse($comment->getLikedBy()->contains($liker));
		self::assertTrue($likeCount > $comment->getLikeCount());
	}
}