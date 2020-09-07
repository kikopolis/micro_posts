<?php

declare(strict_types = 1);

namespace App\Tests\integration\Post;

use App\Entity\Post;
use App\Event\Post\RestoreEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use Codeception\Test\Unit;

/**
 * @coversNothing
 * Class RestoreTest
 * @package App\Tests\integration\Post
 */
class RestoreTest extends Unit
{
	use EventDispatcherConcern;
	use EntityManagerConcern;
	
	public function testRestore()
	{
		$ed = $this->getEd();
		$em = $this->getEm();
		
		$post = $em->find(Post::class, 1);
		
		$ed->dispatch(
			new RestoreEvent($post->getAuthor(), $post)
		);
		
		$em->flush();
		
		// assertions
		static::assertFalse($post->isTrashed());
		
		static::assertEquals(
			$post->getRestoredBy()->getId(),
			$post->getAuthor()->getId()
		);
		
		static::assertNotNull($post->getRestoredAt());
		
		static::assertNull($post->getTrashedBy());
		static::assertNull($post->getTrashedAt());
	}
}