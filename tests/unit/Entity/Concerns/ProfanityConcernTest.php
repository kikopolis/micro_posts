<?php

declare(strict_types = 1);

namespace App\Tests\unit\Entity\Concerns;

use App\Entity\Concerns\ProfanityConcern;
use Codeception\Test\Unit;

/**
 * @covers \App\Entity\Concerns\ProfanityConcern
 * Class ProfanityConcernTest
 * @package App\Tests\unit\Entity\Concerns
 */
class ProfanityConcernTest extends Unit
{
	public function testCleanString()
	{
		/** @var ProfanityConcern $cleaner */
		$cleaner = $this->getMockForTrait(ProfanityConcern::class);
		
		$dirty = 'fuckshitcunt';
		
		$clean = $cleaner->cleanString($dirty);
		
		$this->assertEquals('************', $clean);
	}
	
	public function testFilterProfanities()
	{
		/** @var ProfanityConcern $cleaner */
		$cleaner = $this->getMockForTrait(ProfanityConcern::class);
		
		$dirty = 'fuckshitcunt';
		
		$clean = $cleaner->filterProfanities($dirty);
		
		$this->assertEquals('************', $clean);
	}
	
	public function testContainsProfanitiesWClean()
	{
		/** @var ProfanityConcern $cleaner */
		$cleaner = $this->getMockForTrait(ProfanityConcern::class);
		
		$dirty = 'clean';
		
		$clean = $cleaner->containsProfanities($dirty);
		
		$this->assertFalse($clean);
	}
	
	public function testContainsProfanitiesWDirty()
	{
		/** @var ProfanityConcern $cleaner */
		$cleaner = $this->getMockForTrait(ProfanityConcern::class);
		
		$dirty = 'fuckshitcunt';
		
		$clean = $cleaner->containsProfanities($dirty);
		
		$this->assertTrue($clean);
	}
}