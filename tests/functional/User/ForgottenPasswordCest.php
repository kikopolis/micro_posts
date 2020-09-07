<?php

declare(strict_types = 1);

namespace App\Tests\functional\User;

use App\Entity\User;
use App\Tests\FunctionalTester;
use Codeception\Util\Fixtures;

/**
 * @covers  \App\Controller\User\ForgottenPassword
 * Class ForgottenPasswordCest
 * @package App\Tests\functional\User
 */
class ForgottenPasswordCest
{
	/**
	 * @param  FunctionalTester  $I
	 */
	public function testForgottenPassword(FunctionalTester $I)
	{
		$userData = Fixtures::get('testUser');
		
		/** @var User $user */
		$user = $I->grabEntityFromRepository(
			User::class,
			[
				'username' => $userData['username'],
			]
		);
		
		$I->assertNull($user->getPasswordResetToken());
		
		$I->amOnPage('/forgot-password');
		
		$I->seeResponseCodeIs(200);
		
		$I->see('Request a password reset', 'h1');
		$I->see(
			'Enter your account email and we will send you a verification token that you can use to change your password.',
			'p'
		);
		
		$I->fillField('user_new_action_code[email]', $user->getEmail());
		
		$I->click('user_new_action_code[submit]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		$I->see(
			'You have successfully requested a password reset token. Please check your email and '
			. 'follow the instructions. It may take some time for the email to arrive.',
			'p'
		);
		
		/** @var User $user */
		$user = $I->grabEntityFromRepository(
			User::class,
			[
				'username' => $userData['username'],
			]
		);
		
		$I->assertNotNull($user->getPasswordResetToken());
	}
}