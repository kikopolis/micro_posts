<?php

declare(strict_types = 1);

namespace App\Tests\functional\User;

use App\Entity\User;
use App\Tests\FunctionalTester;
use Codeception\Util\Fixtures;

/**
 * @covers  \App\Controller\User\ActivationToken
 * Class ActivationTokenCest
 * @package App\Tests\functional\User
 */
class ActivationTokenCest
{
	/**
	 * @param  FunctionalTester  $I
	 */
	public function testActivationToken(FunctionalTester $I)
	{
		$userData = Fixtures::get('testUser');
		
		/** @var User $user */
		$user = $I->grabEntityFromRepository(
			User::class,
			[
				'username' => $userData['username'],
			]
		);
		
		$oldToken = $user->getAccountActivationToken();
		
		$I->amOnPage('/request-new-activation-token');
		
		$I->seeResponseCodeIs(200);
		
		$I->see('Get a new account activation token', 'h1');
		$I->see(
			'Enter your email and we will send you a new code. Please allow some time for the email to arrive.',
			'p'
		);
		
		$I->fillField('user_new_action_code[email]', $user->getEmail());
		
		$I->click('user_new_action_code[submit]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		$I->see(
			'The new code has been sent to your email. Give it some time and check the email!',
			'p'
		);
		
		/** @var User $user */
		$user = $I->grabEntityFromRepository(
			User::class,
			[
				'username' => $userData['username'],
			]
		);
		
		$I->assertNotNull($user->getAccountActivationToken());
		
		$I->assertNotEquals(
			$oldToken,
			$user->getAccountActivationToken()
		);
	}
}