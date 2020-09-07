<?php

declare(strict_types = 1);

namespace App\Tests\functional\User;

use App\Entity\User;
use App\Tests\functional\Concerns\LoginConcern;
use App\Tests\FunctionalTester;
use Codeception\Util\Fixtures;

/**
 * @covers  \App\Controller\User\Activate
 * Class ActivateCest
 * @package App\Tests\functional\User
 */
class ActivateCest
{
	use LoginConcern;
	
	/**
	 * @param  FunctionalTester  $I
	 */
	public function testActivation(FunctionalTester $I)
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
		
		$this->login($I, $userData['username'], $userData['password']);
	}
}