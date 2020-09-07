<?php

declare(strict_types = 1);

namespace App\Tests\functional\User;

use App\Entity\User;
use App\Tests\functional\Concerns\LoginConcern;
use App\Tests\FunctionalTester;
use Codeception\Util\Fixtures;

/**
 * @covers  \App\Controller\User\Update
 * Class UpdateCest
 * @package App\Tests\functional\User
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
		
		$data = [
			'newFullName' => 'Kiko Kikopolis',
			'newEmail'    => 'kiko@kiko.tech',
			'newPassword' => 'SecreEtSuperSECRET1',
		];
		
		$oldPwdHash = $I->grabEntityFromRepository(
			User::class,
			[
				'username' => $userData['username'],
			]
		)->getPassword()
		;
		
		$this->login($I, $userData['username'], $userData['password']);
		
		$I->amOnPage('/users/self/update');
		
		$I->seeResponseCodeIs(200);
		
		$I->fillField('user_edit[fullName]', $data['newFullName']);
		$I->fillField('user_edit[email]', $data['newEmail']);
		$I->fillField('user_edit[plainPassword]', $data['newPassword']);
		$I->fillField('user_edit[retypedPlainPassword]', $data['newPassword']);
		
		$I->click('user_edit[submit]');
		
		$I->expect('form us submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		/** @var User $user */
		$user = $I->grabEntityFromRepository(
			User::class,
			[
				'username' => $userData['username'],
			]
		);
		
		$I->assertEquals(
			$userData['username'],
			$user->getUsername()
		);
		
		$I->assertEquals(
			$data['newFullName'],
			$user->getFullName()
		);
		
		$I->assertEquals(
			$data['newEmail'],
			$user->getEmail()
		);
		
		$I->assertEquals(
			$userData['email'],
			$user->getOldEmail()
		);
		
		$I->assertNotEquals(
			$oldPwdHash,
			$user->getPassword()
		);
		
		$I->assertNotEquals(
			$data['newPassword'],
			$user->getPassword()
		);
		
		$I->assertNull($user->getPlainPassword());
		
		$I->assertNull($user->getRetypedPlainPassword());
	}
}