<?php

declare(strict_types = 1);

namespace App\Tests\integration\Comment;

use App\Entity\Comment;
use App\Entity\Notification\CommentLikeNotification;
use App\Entity\User;
use App\Event\Comment\LikeEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use App\Tests\IntegrationTester;
use Codeception\Test\Unit;

/**
 * @coversNothing
 * Class LikeTest
 * @package App\Tests\integration\Comment
 */
class LikeTest extends Unit
{
	use EventDispatcherConcern;
	use EntityManagerConcern;
	
	/**
	 * @var IntegrationTester
	 */
	protected IntegrationTester $tester;
	
	public function testLike()
	{
		$ed = $this->getEd();
		$em = $this->getEm();
		
		$comment = $em->find(Comment::class, 1);
		
		$liker = $em->find(User::class, 1);
		
		static::assertNotEquals(
			$comment->getAuthor()->getId(),
			$liker->getId()
		);
		
		$likeCount = $comment->getLikeCount();
		
		$ed->dispatch(
			new LikeEvent($liker, $comment)
		);
		
		$em->flush();
		
		// assertions
		static::assertTrue($comment->getLikedBy()->contains($liker));
		static::assertTrue($likeCount < $comment->getLikeCount());
		
		$this->tester->canSeeInRepository(
			CommentLikeNotification::class,
			[
				'owner' => $comment->getAuthor(),
			]
		);
	}
}