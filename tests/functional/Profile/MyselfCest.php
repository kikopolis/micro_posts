<?php

declare(strict_types = 1);

namespace App\Tests\functional\Profile;

use App\Entity\User;
use App\Tests\functional\Concerns\LoginConcern;
use App\Tests\FunctionalTester;
use Codeception\Util\Fixtures;

/**
 * @covers  \App\Controller\Profile\Myself
 * Class MyselfCest
 * @package App\Tests\functional\Profile
 */
class MyselfCest
{
	use LoginConcern;
	
	/**
	 * @param  FunctionalTester  $I
	 */
	public function testMyself(FunctionalTester $I)
	{
		$userData = Fixtures::get('activeTestUser');
		
		$this->login($I, $userData['username'], $userData['password']);
		
		/** @var User $user */
		$user = $I->grabEntityFromRepository(
			User::class,
			[
				'username' => $userData['username'],
			]
		);
		
		$I->amOnPage('/profile');
		
		$I->seeResponseCodeIs(200);
		
		$I->see($user->getProfile()->getBio(), 'p[class=text-black-50]');
		$I->see('Username - ' . $user->getUsername(), 'p[class=text-black-50]');
		$I->see('Full name - ' . $user->getFullName(), 'p[class=text-black-50]');
	}
}