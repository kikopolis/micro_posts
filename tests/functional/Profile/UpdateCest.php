<?php

declare(strict_types = 1);

namespace App\Tests\functional\Profile;

use App\Entity\User;
use App\Tests\functional\Concerns\LoginConcern;
use App\Tests\FunctionalTester;
use Codeception\Util\Fixtures;

/**
 * @covers  \App\Controller\User\Update
 * Class UpdateCest
 * @package App\Tests\functional\Profile
 */
class UpdateCest
{
	use LoginConcern;
	
	/**
	 * @param  FunctionalTester  $I
	 */
	public function testUpdate(FunctionalTester $I)
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
		
		$I->amOnPage('/profile/self/update');
		
		$I->seeResponseCodeIs(200);
		
		$newAvatar = 'image.jpg';
		$newBio    = 'this is a new bio';
		
		$I->attachFile('profile_edit[avatar]', $newAvatar);
		$I->fillField('profile_edit[bio]', $newBio);
		
		$I->click('profile_edit[submit_changes]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		/** @var User $modifiedUser */
		$modifiedUser = $I->grabEntityFromRepository(
			User::class,
			[
				'username' => $userData['username'],
			]
		);
		
		$I->assertEquals(
			$newBio,
			$modifiedUser->getProfile()->getBio()
		);
		
		$I->assertNotEquals(
			$user->getProfile()->getBio(),
			$modifiedUser->getProfile()->getBio()
		);
		
		$I->assertNotEquals(
			$user->getProfile()->getAvatar(),
			$modifiedUser->getProfile()->getAvatar()
		);
	}
}