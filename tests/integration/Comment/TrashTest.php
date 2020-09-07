<?php

declare(strict_types = 1);

namespace App\Tests\integration\Comment;

use App\Entity\Comment;
use App\Event\Comment\TrashEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use Codeception\Test\Unit;

/**
 * @coversNothing
 * Class TrashTest
 * @package App\Tests\integration\Comment
 */
class TrashTest extends Unit
{
	use EventDispatcherConcern;
	use EntityManagerConcern;
	
	public function testTrash()
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
	}
}