<?php

declare(strict_types = 1);

namespace App\Tests\unit\Entity\Concerns;

use App\Entity\Concerns\ViewableConcern;
use Codeception\Test\Unit;

/**
 * @covers \App\Entity\Concerns\ViewableConcern
 * Class ViewableConcernTest
 * @package App\Tests\unit\Entity\Concerns
 */
class ViewableConcernTest extends Unit
{
	public function testViewCounters()
	{
		$viewable = $this->getMockForTrait(ViewableConcern::class);
		
		$viewable->viewCount        = 0;
		$viewable->weeklyViewCount  = 0;
		$viewable->monthlyViewCount = 0;
		
		$viewable->incrementViewCounters();
		
		// assertions
		$this->assertEquals(1, $viewable->getViewCount());
		$this->assertEquals(1, $viewable->getWeeklyViewCount());
		$this->assertEquals(1, $viewable->getMonthlyViewCount());
		
		$viewable->resetMonthlyViewCount();
		$viewable->resetWeeklyViewCount();
		
		$viewable->incrementViewCounters();
		
		// assertions
		$this->assertEquals(2, $viewable->getViewCount());
		$this->assertEquals(1, $viewable->getWeeklyViewCount());
		$this->assertEquals(1, $viewable->getMonthlyViewCount());
	}
}