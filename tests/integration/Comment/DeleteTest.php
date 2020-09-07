<?php

declare(strict_types = 1);

namespace App\Tests\integration\Comment;

use App\Entity\Comment;
use App\Event\Comment\DeleteEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use Codeception\Test\Unit;

/**
 * Class DeleteTest
 * @package App\Tests\integration\Comment
 */
class DeleteTest extends Unit
{
	use EventDispatcherConcern;
	use EntityManagerConcern;
	
	public function testDelete()
	{
		$em = $this->getEm();
		$ed = $this->getEd();
		
		$comment = $em->find(Comment::class, 1);
		
		$ed->dispatch(
			new DeleteEvent($comment->getAuthor(), $comment)
		);
		
		$em->flush();
		
		// assertions
		self::assertNull(
			$em->find(Comment::class, 1)
		);
	}
}