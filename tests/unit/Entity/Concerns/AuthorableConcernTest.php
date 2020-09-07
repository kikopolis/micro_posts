<?php

declare(strict_types = 1);

namespace App\Tests\unit\Entity\Concerns;

use App\Entity\Concerns\AuthorableConcern;
use App\Entity\User;
use Codeception\Stub;
use Codeception\Test\Unit;

/**
 * @covers \App\Entity\Concerns\AuthorableConcern
 * Class AuthorableConcernTest
 * @package App\Tests\unit\Entity\Concerns
 */
class AuthorableConcernTest extends Unit
{
	public function testSetAuthor()
	{
		/** @var User $author */
		$author = Stub::make(
			User::class,
			[
				'getId' => 20039,
			],
			$this
		);
		
		$authorable = $this->getMockForTrait(AuthorableConcern::class);
		
		$authorable->setAuthor($author);
		
		$this->assertEquals(
			$author->getId(),
			$authorable->getAuthor()->getId()
		);
	}
}