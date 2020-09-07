<?php

declare(strict_types = 1);

namespace App\Tests\unit\Entity\Concerns;

use App\Entity\Concerns\LikableConcern;
use App\Entity\User;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

/**
 * @covers  \App\Entity\Concerns\LikableConcern
 * Class LikableConcernTest
 * @package App\Tests\unit\Entity\Concerns
 */
class LikableConcernTest extends Unit
{
	/**
	 * @throws Exception
	 */
	public function testLikeAndUnlike()
	{
		/** @var User $liker */
		$liker = Stub::make(
			User::class,
			[
				'getId' => 9984,
			],
			$this
		);
		
		$likable = $this->getMockForTrait(LikableConcern::class);
		
		$likable->likedBy         = new ArrayCollection();
		$likable->likeCount       = 0;
		$likable->weeklyLikeCount = 0;
		
		$likable->like($liker);
		
		// assertions
		static::assertTrue($likable->getLikedBy()->contains($liker));
		static::assertEquals(1, $likable->getLikeCount());
		static::assertEquals(1, $likable->getWeeklyLikeCount());
		
		$likable->unlike($liker);
		
		// assertions
		static::assertFalse($likable->getLikedBy()->contains($liker));
		static::assertEquals(0, $likable->getLikeCount());
		static::assertEquals(0, $likable->getWeeklyLikeCount());
	}
}