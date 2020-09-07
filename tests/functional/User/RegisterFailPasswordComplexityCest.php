<?php

declare(strict_types = 1);

namespace App\Tests\functional\User;

use App\Tests\FunctionalTester;

/**
 * @covers  \App\Controller\User\Register
 * Class RegisterCest
 * @package App\Tests\functional\User
 */
class RegisterFailPasswordComplexityCest
{
	/**
	 * @param  FunctionalTester  $I
	 */
	public function testRegisterSuccess(FunctionalTester $I)
	{
		$I->amOnPage('/register');
		
		$I->seeResponseCodeIs(200);
		
		$I->see('Register for a new account', 'h1');
		
		$I->fillField('user_register[username]', 'usernameUniiq');
		$I->fillField('user_register[fullname]', 'Test User');
		$I->fillField('user_register[email]', 'dry-test-user@mail.com');
		$I->fillField('user_register[plainPassword]', 'secret');
		$I->fillField('user_register[retypedPlainPassword]', 'secret');
		
		$I->checkOption('user_register[termsAgreed]');
		
		$I->click('user_register[register]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		$I->see('Minimum length is 8. The password must also contain one uppercase, one lowercase letter and one digit.');
	}
}