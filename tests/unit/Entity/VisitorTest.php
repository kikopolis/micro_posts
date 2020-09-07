<?php

declare(strict_types = 1);

namespace App\Tests\unit\Entity;

use App\Entity\Visitor;
use Codeception\Test\Unit;

/**
 * @covers \App\Entity\Visitor
 * Class VisitorTest
 * @package App\Tests\unit\Entity
 */
class VisitorTest extends Unit
{
	public function testDefaultProps()
	{
		$visitor = new Visitor();
		
		$this->assertNull($visitor->getId());
		$this->assertNull($visitor->getClientIp());
		$this->assertNull($visitor->getRoute());
		$this->assertNull($visitor->getController());
		$this->assertNull($visitor->getBrowser());
		$this->assertNull($visitor->getCreatedAt());
		$this->assertNull($visitor->getUpdatedAt());
	}
	
	public function testConstructorParams()
	{
		$ip         = '192.168.0.1';
		$route      = '/';
		$controller = 'HomeController';
		$browser    = 'Mozilla/Firefox';
		
		$visitor = new Visitor(
			$ip,
			$route,
			$controller,
			$browser
		);
		
		$this->assertEquals(
			$ip,
			$visitor->getClientIp()
		);
		$this->assertEquals(
			$route,
			$visitor->getRoute()
		);
		$this->assertEquals(
			$controller,
			$visitor->getController()
		);
		$this->assertEquals(
			$browser,
			$visitor->getBrowser()
		);
	}
}