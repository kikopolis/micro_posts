<?php

declare(strict_types = 1);

namespace App\Tests\unit\Entity\Concerns;

use App\Entity\Concerns\PublishableConcern;
use App\Entity\User;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;

/**
 * @covers \App\Entity\Concerns\PublishableConcern
 * Class PublishableConcern
 * @package App\Tests\unit\Entity\Concerns
 */
class PublishableConcernTest extends Unit
{
	/**
	 * @throws Exception
	 */
	public function testPublishAndUnPublish()
	{
		/** @var User $publishedBy */
		$publishedBy = Stub::make(
			User::class,
			[
				'getId' => 555,
			],
			$this
		);
		
		$publishable = $this->getMockForTrait(PublishableConcern::class);
		
		$publishable->publishedAt = null;
		$publishable->publishedBy = null;
		
		$publishable->publish();
		$publishable->setPublishedBy($publishedBy);
		
		// assertions
		$this->assertTrue($publishable->isPublished());
		$this->assertEquals(
			$publishedBy->getId(),
			$publishable->getPublishedBy()->getId()
		);
		
		$publishable->unPublish();
		
		// assertions
		$this->assertFalse($publishable->isPublished());
		$this->assertEquals(null, $publishable->getPublishedBy());
		$this->assertEquals(null, $publishable->getPublishedAt());
	}
}