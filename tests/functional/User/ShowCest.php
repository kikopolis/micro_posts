<?php

declare(strict_types = 1);

namespace App\Tests\functional\User;

use App\Tests\functional\Concerns\LoginConcern;
use App\Tests\FunctionalTester;
use Codeception\Util\Fixtures;

/**
 * @covers  \App\Controller\User\Show
 * Class ShowCest
 * @package App\Tests\functional\User
 */
class ShowCest
{
	use LoginConcern;
	
	/**
	 * @param  FunctionalTester  $I
	 */
	public function testShow(FunctionalTester $I)
	{
		$user = Fixtures::get('activeTestUser');
		
		$this->login($I, $user['username'], $user['password']);
		
		$I->amOnPage('/users');
		
		$I->seeResponseCodeIs(200);
		
		$I->seeLink('Account', '/users/self/update');
		$I->seeLink('Profile', '/profile/self/update');
		$I->seeLink('Settings', '/preferences/edit');
		$I->seeLink('Trash Account', '/users/trash');
		
		$I->see('Account', 'h4');
		$I->see('Email - ' . $user['email'], 'p');
		$I->see('Full Name - ' . $user['fullName'], 'p');
		$I->see('Username - ' . $user['username'], 'p');
	}
}