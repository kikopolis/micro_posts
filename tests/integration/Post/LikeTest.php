<?php

declare(strict_types = 1);

namespace App\Tests\integration\Post;

use App\Entity\Notification\PostLikeNotification;
use App\Entity\Post;
use App\Entity\User;
use App\Event\Post\LikeEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use App\Tests\IntegrationTester;
use Codeception\Test\Unit;

/**
 * @coversNothing
 * Class LikeTest
 * @package App\Tests\integration\Post
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
	}
}