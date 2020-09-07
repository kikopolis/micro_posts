<?php

declare(strict_types = 1);

namespace App\Tests\integration\Post;

use App\Entity\Notification\PostLikeNotification;
use App\Entity\Post;
use App\Entity\User;
use App\Event\Post\DeleteEvent;
use App\Event\Post\LikeEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use App\Tests\IntegrationTester;
use Codeception\Test\Unit;

/**
 * @coversNothing
 * Class DeleteWithLikesTest
 * @package App\Tests\integration\Post
 */
class DeleteWithLikesTest extends Unit
{
	use EventDispatcherConcern;
	use EntityManagerConcern;
	
	/**
	 * @var IntegrationTester
	 */
	protected IntegrationTester $tester;
	
	public function testDeleteWithLikes()
	{
		$ed = $this->getEd();
		$em = $this->getEm();
		
		$post = $em->find(Post::class, 1);
		
		$user1 = $em->find(User::class, 1);
		
		$user2 = $em->find(User::class, 2);
		
		$user3 = $em->find(User::class, 3);
		
		$ed->dispatch(
			new LikeEvent($user1, $post)
		);
		
		$ed->dispatch(
			new LikeEvent($user2, $post)
		);
		
		$ed->dispatch(
			new LikeEvent($user3, $post)
		);
		
		$em->flush();
		
		// assertions
		$this->tester->canSeeInRepository(
			PostLikeNotification::class,
			[
				'owner' => $post->getAuthor(),
			]
		);
		
		static::assertTrue($post->getLikedBy()->contains($user1));
		
		static::assertTrue($post->getLikedBy()->contains($user2));
		
		static::assertTrue($post->getLikedBy()->contains($user3));
		
		$ed->dispatch(
			new DeleteEvent($post->getAuthor(), $post)
		);
		
		$em->flush();
		
		// assertions
		self::assertNull(
			$em->find(Post::class, 1)
		);
		
		$this->tester->cantSeeInRepository(
			PostLikeNotification::class,
			[
				'owner' => $post->getAuthor(),
			]
		);
	}
}