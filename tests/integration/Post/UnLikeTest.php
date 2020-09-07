<?php

declare(strict_types = 1);

namespace App\Tests\integration\Post;

use App\Entity\Notification\PostLikeNotification;
use App\Entity\Post;
use App\Entity\User;
use App\Event\Post\LikeEvent;
use App\Event\Post\UnLikeEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use App\Tests\IntegrationTester;
use Codeception\Test\Unit;

/**
 * @coversNothing
 * Class UnLikeTest
 * @package App\Tests\integration\Post
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
		
		$post = $em->find(Post::class, 1);
		
		$liker = $em->find(User::class, 1);
		
		static::assertNotEquals(
			$post->getAuthor()->getId(),
			$liker->getId()
		);
		
		$likeCount = $post->getLikeCount();
		
		$ed->dispatch(
			new LikeEvent($liker, $post)
		);
		
		$em->flush();
		
		// assertions
		static::assertTrue($post->getLikedBy()->contains($liker));
		static::assertTrue($likeCount < $post->getLikeCount());
		
		$this->tester->canSeeInRepository(
			PostLikeNotification::class,
			[
				'owner' => $post->getAuthor(),
			]
		);
		
		// get the like count again to reflect the user having liked the post
		$likeCount = $post->getLikeCount();
		
		$ed->dispatch(
			new UnLikeEvent($liker, $post)
		);
		
		$em->flush();
		
		static::assertFalse($post->getLikedBy()->contains($liker));
		static::assertTrue($likeCount > $post->getLikeCount());
	}
}