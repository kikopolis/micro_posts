<?php

declare(strict_types = 1);

namespace App\Tests\functional\User;

use App\Entity\User;
use App\Tests\functional\Concerns\LoginConcern;
use App\Tests\FunctionalTester;
use Codeception\Util\Fixtures;

/**
 * @covers  \App\Controller\User\PasswordChange
 * Class PasswordChangeCest
 * @package App\Tests\functional\User
 */
class PasswordChangeCest
{
	use LoginConcern;
	
	/**
	 * @param  FunctionalTester  $I
	 */
	public function testPasswordChange(FunctionalTester $I)
	{
		$userData = Fixtures::get('testUser');
		
		/** @var User $user */
		$user = $I->grabEntityFromRepository(
			User::class,
			[
				'username' => $userData['username'],
			]
		);
		
		$I->amOnPage(sprintf('/account/confirm/%s', $user->getAccountActivationToken()));
		
		$I->seeResponseCodeIs(200);
		
		$I->see('Account activated. You may now login!', 'p');
		
		/** @var User $user */
		$user = $I->grabEntityFromRepository(
			User::class,
			[
				'username' => $userData['username'],
			]
		);
		
		$I->assertTrue($user->isActivated());
		$I->assertNull($user->getAccountActivationToken());
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
		
		$I->amOnPage(sprintf('/%s/change-password', $user->getPasswordResetToken()));
		
		$I->seeResponseCodeIs(200);
		
		$I->see('Change your password', 'h1');
		$I->see('Enter a new password according to security guidelines', 'p');
		
		$I->fillField('user_change_password[plainPassword]', $userData['newPassword']);
		$I->fillField('user_change_password[retypedPlainPassword]', $userData['newPassword']);
		
		$I->click('user_change_password[save_new_password]');
		
		$I->see('Your password has been changed successfully. You may now log in with the new password.', 'p');
		
		/** @var User $user */
		$user = $I->grabEntityFromRepository(
			User::class,
			[
				'username' => $userData['username'],
			]
		);
		
		$I->assertNull($user->getPasswordResetToken());
		$I->assertTrue($user->isActivated());
		
		$this->login($I, $userData['username'], $userData['newPassword']);
		
		$I->seeResponseCodeIs(200);
	}
}