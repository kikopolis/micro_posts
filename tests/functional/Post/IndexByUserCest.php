<?php

declare(strict_types = 1);

namespace App\Tests\functional\Post;

use App\Entity\User;
use App\Tests\FunctionalTester;
use Codeception\Util\Fixtures;

/**
 * @covers  \App\Controller\Post\IndexByUser
 * Class IndexByUserCest
 * @package App\Tests\functional\Post
 */
class IndexByUserCest
{
	/**
	 * @param  FunctionalTester  $I
	 */
	public function testIndexByUser(FunctionalTester $I)
	{
		$userData = Fixtures::get('activeTestUser');
		
		/** @var User $user */
		$user = $I->grabEntityFromRepository(
			User::class,
			[
				'username' => $userData['username'],
			]
		);
		
		$I->amOnPage("/posts/{$user->getUsername()}");
		
		$I->seeResponseCodeIs(200);
		
		$I->seeInSource("<title>Posts for user {$user->getFullName()}</title>");
	}
}