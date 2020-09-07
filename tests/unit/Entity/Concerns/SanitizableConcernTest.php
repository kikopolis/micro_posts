<?php

declare(strict_types = 1);

namespace App\Tests\unit\Entity\Concerns;

use App\Entity\Concerns\SanitizableConcern;
use Codeception\Test\Unit;

/**
 * @covers \App\Entity\Concerns\SanitizableConcern
 * Class SanitizableConcernTest
 * @package App\Tests\unit\Entity\Concerns
 */
class SanitizableConcernTest extends Unit
{
	public function testSanitize()
	{
		$dirty = 'string<script src="http://googl.com/bad-js.js"></script>'
			. '<script>alert(\'alert\');</script>'
			. '<h1>_HEADING</h1>'
			. '<p>_PARAGRAPH</p>'
			. '<a href="http://googl.com">_LINK</a>'
			. '<a href="http://googl.com">_LINK<script>alert(\'alert\');</script></a>'
			. '<img src="http://googl.com/image.jpg" alt="image" />';
		
		$expected = 'string'
			. '_HEADING'
			. '_PARAGRAPH'
			. '<a href="http://googl.com">_LINK</a>'
			. '<a href="http://googl.com">_LINK</a>'
			. '<img src="http://googl.com/image.jpg" alt="image" />';
		
		$sanitizer = $this->getMockForTrait(SanitizableConcern::class);
		
		$clean = $sanitizer->sanitize($dirty);
		
		$this->assertEquals($expected, $clean);
	}
	
	public function testPurify()
	{
		$dirty = 'string<script src="http://googl.com/bad-js.js"></script>'
			. '<script>alert(\'alert\');</script>'
			. '<h1>_HEADING</h1>'
			. '<p>_PARAGRAPH</p>'
			. '<a href="http://googl.com">_LINK</a>'
			. '<a href="http://googl.com">_LINK<script>alert(\'alert\');</script></a>'
			. '<img src="http://googl.com/image.jpg" alt="image" />';
		
		$expected = 'string'
			. '_HEADING'
			. '_PARAGRAPH'
			. '_LINK'
			. '_LINK';
		
		$sanitizer = $this->getMockForTrait(SanitizableConcern::class);
		
		$clean = $sanitizer->purify($dirty);
		
		$this->assertEquals($expected, $clean);
	}
}