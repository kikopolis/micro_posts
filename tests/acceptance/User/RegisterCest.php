<?php

declare(strict_types = 1);

namespace App\Tests\acceptance\User;

use App\Tests\AcceptanceTester;

/**
 * @covers  \App\Controller\User\Register
 * Class RegisterCest
 * @package App\Tests\acceptance
 */
class RegisterCest
{
	/**
	 * @param  AcceptanceTester  $I
	 */
	public function registerWorks(AcceptanceTester $I)
	{
		$I->amOnPage('/register');
		$I->see('Register for a new account', 'h1');
		$I->fillField('user_register[username]', 'testUser' . rand(0, 100));
		$I->fillField('user_register[fullname]', 'Test User');
		$I->fillField('user_register[email]', 'test.user' . rand(0, 100) . '@test.com');
		$I->fillField('user_register[plainPassword]', 'SecretSecret65');
		$I->fillField('user_register[retypedPlainPassword]', 'SecretSecret65');
		$I->checkOption('user_register[termsAgreed]');
		$I->click('user_register[register]');
		$I->expect('form is submitted correctly');
		$I->see('Account created! We have sent you an email with instructions to activate your account.');
	}
}