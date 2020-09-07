<?php

declare(strict_types = 1);

namespace App\Tests\functional\User;

use App\Tests\functional\Concerns\LoginConcern;
use App\Tests\FunctionalTester;
use Codeception\Util\Fixtures;

/**
 * @covers \App\Controller\User\Login
 * Class LoginCest
 * @package App\Tests\functional\User
 */
class LoginCest
{
	use LoginConcern;
	
	/**
	 * @param  FunctionalTester  $I
	 */
	public function testLogin(FunctionalTester $I)
	{
		$user = Fixtures::get('activeTestUser');
		
		$this->login($I, $user['username'], $user['password']);
		
		$I->seeResponseCodeIs(200);
	}
}