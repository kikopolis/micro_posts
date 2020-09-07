<?php

declare(strict_types = 1);

namespace App\Tests\functional\User;

use App\Entity\User;
use App\Tests\functional\Concerns\LoginConcern;
use App\Tests\FunctionalTester;
use Codeception\Util\Fixtures;

/**
 * @covers \App\Controller\User\UnFollow
 * Class UnFollowCest
 * @package App\Tests\functional\User
 */
class UnFollowCest
{
	use LoginConcern;
	/**
	 * @param  FunctionalTester  $I
	 */
	public function testUnFollow(FunctionalTester $I)
	{
		$activeUser = Fixtures::get('activeTestUser');
		$testUser   = Fixtures::get('testUser');
		
		$this->login($I, $activeUser['username'], $activeUser['password']);
		
		$I->amOnPage('/profile/testUser');
		
		$I->seeResponseCodeIs(200);
		
		$I->seeLink('Follow');
		$I->click('Follow');
		
		$I->seeResponseCodeIs(200);
		
		/** @var User $followed */
		$followed = $I->grabEntityFromRepository(
			User::class,
			[
				'username' => $testUser['username'],
			]
		);
		
		/** @var User $follower */
		$follower = $I->grabEntityFromRepository(
			User::class,
			[
				'username' => $activeUser['username'],
			]
		);
		
		$I->assertTrue(
			$follower->getFollowing()->contains($followed)
		);
		
		$I->assertTrue(
			$followed->getFollowers()->contains($follower)
		);
		
		$I->amOnPage('/profile/testUser');
		
		$I->seeResponseCodeIs(200);
		
		$I->seeLink('Un-follow');
		$I->click('Un-follow');
		
		$I->seeResponseCodeIs(200);
		
		/** @var User $followed */
		$followed = $I->grabEntityFromRepository(
			User::class,
			[
				'username' => $testUser['username'],
			]
		);
		
		/** @var User $follower */
		$follower = $I->grabEntityFromRepository(
			User::class,
			[
				'username' => $activeUser['username'],
			]
		);
		
		$I->assertFalse(
			$follower->getFollowing()->contains($followed)
		);
		
		$I->assertFalse(
			$followed->getFollowers()->contains($follower)
		);
	}
}