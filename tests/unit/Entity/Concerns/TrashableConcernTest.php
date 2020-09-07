<?php

declare(strict_types = 1);

namespace App\Tests\unit\Entity\Concerns;

use App\Entity\Concerns\TrashableConcern;
use App\Entity\User;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;

/**
 * @covers \App\Entity\Concerns\TrashableConcern
 * Class TrashableConcernTest
 * @package App\Tests\unit\Entity\Concerns
 */
class TrashableConcernTest extends Unit
{
	/**
	 * @throws Exception
	 */
	public function testTrashAndUnTrash()
	{
		/** @var User $trashedBy */
		$trashedBy = Stub::make(
			User::class,
			[
				'getId' => 7564,
			],
			$this
		);
		
		$trashable = $this->getMockForTrait(TrashableConcern::class);
		
		$trashable->trashedAt  = null;
		$trashable->trashedBy  = null;
		$trashable->restoredAt = null;
		$trashable->restoredBy = null;
		
		$trashable->trash();
		$trashable->setTrashedBy($trashedBy);
		
		// assertions
		$this->assertTrue($trashable->isTrashed());
		$this->assertEquals(
			$trashedBy->getId(),
			$trashable->getTrashedBy()->getId()
		);
		$this->assertEquals(null, $trashable->getRestoredAt());
		$this->assertEquals(null, $trashable->getRestoredBy());
		
		$trashable->restore();
		$trashable->setRestoredBy($trashedBy);
		
		// assertions
		
		$this->assertFalse($trashable->isTrashed());
		$this->assertEquals(
			$trashedBy->getId(),
			$trashable->getRestoredBy()->getId()
		);
		$this->assertEquals(null, $trashable->getTrashedAt());
		$this->assertEquals(null, $trashable->getTrashedBy());
	}
}