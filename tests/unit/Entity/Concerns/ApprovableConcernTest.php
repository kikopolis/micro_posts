<?php

declare(strict_types = 1);

namespace App\Tests\unit\Entity\Concerns;

use App\Entity\Concerns\ApprovableConcern;
use App\Entity\User;
use Codeception\Stub;
use Codeception\Test\Unit;

/**
 * @covers \App\Entity\Concerns\ApprovableConcern
 * Class ApprovableConcernTest
 * @package App\Tests\unit\Entity\Concerns
 */
class ApprovableConcernTest extends Unit
{
	public function testApproveAndUnApprove()
	{
		/** @var User $approvedBy */
		$approvedBy = Stub::make(
			User::class,
			[
				'getId' => 22341,
			],
			$this
		);
		
		$approvable = $this->getMockForTrait(ApprovableConcern::class);
		
		$approvable->approvedAt   = null;
		$approvable->approvedBy   = null;
		$approvable->unApprovedBy = null;
		
		$approvable->approve();
		$approvable->setApprovedBy($approvedBy);
		
		// assertions
		$this->assertNotNull($approvable->getApprovedAt());
		$this->assertTrue($approvable->isApproved());
		$this->assertEquals(
			$approvedBy->getId(),
			$approvable->getApprovedBy()->getId()
		);
		$this->assertNull($approvable->getUnApprovedBy());
		
		$approvable->unApprove();
		$approvable->setUnApprovedBy($approvedBy);
		
		// assertions
		$this->assertNull($approvable->getApprovedAt());
		$this->assertFalse($approvable->isApproved());
		$this->assertEquals(
			$approvedBy->getId(),
			$approvable->getUnApprovedBy()->getId()
		);
		$this->assertNull($approvable->getApprovedBy());
	}
}