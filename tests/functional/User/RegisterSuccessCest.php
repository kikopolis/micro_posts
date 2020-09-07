<?php

declare(strict_types = 1);

namespace App\Tests\functional\User;

use App\Entity\User;
use App\Tests\FunctionalTester;

/**
 * @covers  \App\Controller\User\Register
 * Class RegisterCest
 * @package App\Tests\functional\User
 */
class RegisterSuccessCest
{
	/**
	 * @param  FunctionalTester  $I
	 */
	public function testRegisterSuccess(FunctionalTester $I)
	{
		$username = 'testDryUser';
		
		$I->amOnPage('/register');
		
		$I->seeResponseCodeIs(200);
		
		$I->see('Register for a new account', 'h1');
		
		$I->fillField('user_register[username]', $username);
		$I->fillField('user_register[fullname]', 'Test User');
		$I->fillField('user_register[email]', 'dry-test-user@mail.com');
		$I->fillField('user_register[plainPassword]', 'SecretSecret65');
		$I->fillField('user_register[retypedPlainPassword]', 'SecretSecret65');
		
		$I->checkOption('user_register[termsAgreed]');
		
		$I->click('user_register[register]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		$I->see('Account created! We have sent you an email with instructions to activate your account.');
		
		$I->seeInRepository(
			User::class,
			[
				'username' => $username,
			]
		);
		
		/** @var User $user */
		$user = $I->grabEntityFromRepository(
			User::class,
			[
				'username' => $username,
			]
		);
		
		$I->assertNull($user->getPlainPassword());
		$I->assertNull($user->getRetypedPlainPassword());
		
		$I->assertFalse($user->isActivated());
		$I->assertNotNull($user->getAccountActivationToken());
	}
}