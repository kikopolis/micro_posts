<?php

declare(strict_types = 1);

namespace App\Tests\integration\Post;

use App\Entity\Post;
use App\Event\Post\TrashEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use Codeception\Test\Unit;

/**
 * @coversNothing
 * Class TrashTest
 * @package App\Tests\integration\Post
 */
class TrashTest extends Unit
{
	use EventDispatcherConcern;
	use EntityManagerConcern;
	
	public function testTrash()
	{
		$ed = $this->getEd();
		$em = $this->getEm();
		
		$post = $em->find(Post::class, 1);
		
		$ed->dispatch(
			new TrashEvent($post->getAuthor(), $post)
		);
		
		$em->flush();
		
		// assertions
		static::assertTrue($post->isTrashed());
		static::assertNotNull($post->getTrashedAt());
		
		static::assertEquals(
			$post->getAuthor()->getId(),
			$post->getTrashedBy()->getId()
		);
		
		static::assertFalse($post->isPublished());
	}
}