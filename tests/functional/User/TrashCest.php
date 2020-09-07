<?php

declare(strict_types = 1);

namespace App\Tests\functional\User;

use App\Tests\functional\Concerns\LoginConcern;
use App\Tests\FunctionalTester;
use Codeception\Util\Fixtures;

/**
 * @covers  \App\Controller\User\Trash
 * Class TrashCest
 * @package App\Tests\functional\User
 */
class TrashCest
{
	use LoginConcern;
	
	/**
	 * @param  FunctionalTester  $I
	 */
	public function testTrash(FunctionalTester $I)
	{
		$user = Fixtures::get('activeTestUser');
		
		$this->login($I, $user['username'], $user['password']);
		
		$I->amOnPage('/users');
		
		$I->seeResponseCodeIs(200);
		
		$I->seeLink('Trash Account', '/users/trash');
		
		$I->click('Trash Account');
		
		$I->seeResponseCodeIs(200);
		
		$I->see(
			'You have successfully trashed your account. You may no longer post and your account will be deleted automatically.',
			'p'
		);
	}
	
}