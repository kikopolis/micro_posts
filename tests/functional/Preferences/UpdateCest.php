<?php

declare(strict_types = 1);

namespace App\Tests\functional\Preferences;

use App\Entity\User;
use App\Entity\UserPreferences;
use App\Tests\functional\Concerns\LoginConcern;
use App\Tests\FunctionalTester;
use Codeception\Util\Fixtures;

/**
 * @covers  \App\Controller\Preferences\Update
 * Class UpdateCest
 * @package App\Tests\functional\Preferences
 */
class UpdateCest
{
	use LoginConcern;
	
	/**
	 * @param  FunctionalTester  $I
	 */
	public function testUpdate(FunctionalTester $I)
	{
		$userData = Fixtures::get('activeTestUser');
		
		$this->login($I, $userData['username'], $userData['password']);
		
		/** @var User $oldUser */
		$oldUser = $I->grabEntityFromRepository(
			User::class,
			[
				'username' => $userData['username'],
			]
		);
		
		$I->amOnPage('/preferences/edit');
		
		$I->seeResponseCodeIs(200);
		
		$I->selectOption(
			'preferences_edit[sortHomePageBy]',
			'Show posts by users I follow, newest first'
		);
		
		$I->click('preferences_edit[save_changes]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		/** @var User $newUser */
		$newUser = $I->grabEntityFromRepository(
			User::class,
			[
				'username' => $userData['username'],
			]
		);
		
		$I->assertEquals(
			UserPreferences::SORT_BY_FOLLOWED_USERS_NEWEST_FIRST,
			$newUser->getPreferences()->getSortHomePageBy()
		);
		
		$I->assertNotEquals(
			$oldUser->getPreferences()->getSortHomePageBy(),
			$newUser->getPreferences()->getSortHomePageBy()
		);
	}
}