<?php

declare(strict_types = 1);

namespace App\Tests\unit\Entity;

use App\Entity\UserPreferences;
use Codeception\Test\Unit;

/**
 * @covers \App\Entity\UserPreferences
 * Class UserPreferencesTest
 * @package App\Tests\unit\Entity
 */
class UserPreferencesTest extends Unit
{
	public function testDefaultProps()
	{
		$preferences = new UserPreferences();
		
		$this->assertNull($preferences->getId());
		$this->assertNull($preferences->getUser());
		$this->assertEquals('en', $preferences->getLocale());
		$this->assertEquals(
			UserPreferences::SORT_BY_ALL_POSTS_NEWEST_FIRST,
			$preferences->getSortHomePageBy()
		);
	}
	
	public function testConstructorParams()
	{
		$locale = 'et';
		$sort   = UserPreferences::SORT_BY_FOLLOWED_USERS_NEWEST_FIRST;
		
		$preferences = new UserPreferences(
			$locale,
			$sort
		);
		
		$this->assertEquals($locale, $preferences->getLocale());
		$this->assertEquals($sort, $preferences->getSortHomePageBy());
	}
}