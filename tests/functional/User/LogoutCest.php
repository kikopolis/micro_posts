<?php

declare(strict_types = 1);

namespace App\Tests\functional\User;

use App\Tests\functional\Concerns\LoginConcern;
use App\Tests\FunctionalTester;
use Codeception\Util\Fixtures;

/**
 * @covers  \App\Controller\User\Logout
 * Class LogoutCest
 * @package App\Tests\functional\User
 */
class LogoutCest
{
	use LoginConcern;
	
	/**
	 * @param  FunctionalTester  $I
	 */
	public function testLogout(FunctionalTester $I)
	{
		$user = Fixtures::get('activeTestUser');
		
		$this->login($I, $user['username'], $user['password']);
		
		$I->seeResponseCodeIs(200);
		
		$I->amOnPage('/logout');
		
		$I->seeResponseCodeIs(200);
		
		$I->amOnPage('/');
		
		$I->seeResponseCodeIs(200);
		
		$I->dontSeeLink('Logout');
		$I->seeLink('Login');
		$I->seeLink('Register');
	}
}