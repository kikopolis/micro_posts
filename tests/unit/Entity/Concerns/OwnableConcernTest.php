<?php

declare(strict_types = 1);

namespace App\Tests\unit\Entity\Concerns;

use App\Entity\Concerns\OwnableConcern;
use App\Entity\User;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;

/**
 * @covers  \App\Entity\Concerns\OwnableConcern
 * Class OwnableConcernTest
 * @package App\Tests\unit\Entity\Concerns
 */
class OwnableConcernTest extends Unit
{
	/**
	 * @throws Exception
	 */
	public function testSetOwner()
	{
		/** @var User $owner */
		$owner = Stub::make(
			User::class,
			[
				'getId' => 20039,
			],
			$this
		);
		
		$authorable = $this->getMockForTrait(OwnableConcern::class);
		
		$authorable->setOwner($owner);
		
		$this->assertEquals(
			$owner->getId(),
			$authorable->getOwner()->getId()
		);
	}
}