<?php

declare(strict_types = 1);

namespace App\Tests\integration\Comment;

use App\Entity\Comment;
use App\Event\Comment\RestoreEvent;
use App\Event\Comment\TrashEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use App\Tests\IntegrationTester;
use Codeception\Test\Unit;

/**
 * @coversNothing
 * Class RestoreTest
 * @package App\Tests\integration\Comment
 */
class RestoreTest extends Unit
{
	use EventDispatcherConcern;
	use EntityManagerConcern;
	
	public function testRestore()
	{
		$ed = $this->getEd();
		$em = $this->getEm();
		
		$comment = $em->find(Comment::class, 1);
		
		$ed->dispatch(
			new TrashEvent($comment->getAuthor(), $comment)
		);
		
		$em->flush();
		
		// assertions
		static::assertTrue($comment->isTrashed());
		static::assertNotNull($comment->getTrashedAt());
		
		static::assertEquals(
			$comment->getTrashedBy()->getId(),
			$comment->getAuthor()->getId()
		);
		
		$ed->dispatch(
			new RestoreEvent($comment->getAuthor(), $comment)
		);
		
		$em->flush();
		
		// assertions
		static::assertFalse($comment->isTrashed());
		static::assertNull($comment->getTrashedBy());
		static::assertNull($comment->getTrashedAt());
		
		static::assertEquals(
			$comment->getRestoredBy()->getId(),
			$comment->getAuthor()->getId()
		);
		
		static::assertNotNull($comment->getRestoredAt());
	}
}