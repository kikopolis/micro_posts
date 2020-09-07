<?php

declare(strict_types = 1);

namespace App\Tests\functional\User;

use App\Tests\FunctionalTester;
use Codeception\Util\Fixtures;

/**
 * @covers  \App\Controller\User\Register
 * Class RegisterCest
 * @package App\Tests\functional\User
 */
class RegisterFailEmailCest
{
	/**
	 * @param  FunctionalTester  $I
	 */
	public function testRegisterSuccess(FunctionalTester $I)
	{
		$email = Fixtures::get('testUser')['email'];
		
		$I->amOnPage('/register');
		
		$I->seeResponseCodeIs(200);
		
		$I->see('Register for a new account', 'h1');
		
		$I->fillField('user_register[username]', 'usernameUniiq');
		$I->fillField('user_register[fullname]', 'Test User');
		$I->fillField('user_register[email]', $email);
		$I->fillField('user_register[plainPassword]', 'SecretSecret65');
		$I->fillField('user_register[retypedPlainPassword]', 'SecretSecret65');
		
		$I->checkOption('user_register[termsAgreed]');
		
		$I->click('user_register[register]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		$I->see('This e-mail is already in use');
	}
}