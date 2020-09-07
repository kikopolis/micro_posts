<?php

declare(strict_types = 1);

namespace App\Tests\unit\Entity\Concerns;

use App\Entity\Concerns\ReportableConcern;
use App\Entity\User;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

/**
 * @covers \App\Entity\Concerns\ReportableConcern
 * Class ReportableConcernTest
 * @package App\Tests\unit\Entity\Concerns
 */
class ReportableConcernTest extends Unit
{
	/**
	 * @throws Exception
	 */
	public function testReportAndUnReport()
	{
		/** @var User $reportedBy */
		$reportedBy = Stub::make(User::class);
		
		$reportable = $this->getMockForTrait(ReportableConcern::class);
		
		$reportable->reported    = false;
		$reportable->reportedBy  = new ArrayCollection();
		$reportable->reportCount = 0;
		
		$reportable->report($reportedBy);
		
		// assertions
		$this->assertTrue($reportable->isReported());
		$this->assertTrue($reportable->getReportedBy()->count() > 0);
		$this->assertEquals(1, $reportable->getReportCount());
		
		$reportable->unReport();
		
		// assertions
		$this->assertFalse($reportable->isReported());
		$this->assertTrue($reportable->getReportedBy()->count() === 0);
		$this->assertEquals(0, $reportable->getReportCount());
	}
}