<?php

declare(strict_types = 1);

namespace App\Tests\functional\Profile;

use App\Entity\User;
use App\Tests\FunctionalTester;

/**
 * @covers  \App\Controller\Profile\ShowById
 * Class ShowByIdCest
 * @package App\Tests\functional\Profile
 */
class ShowByIdCest
{
	/**
	 * @param  FunctionalTester  $I
	 */
	public function testShowById(FunctionalTester $I)
	{
		/** @var User $user */
		$user = $I->grabEntityFromRepository(
			User::class,
			[
				'id' => 1,
			]
		);
		
		$I->amOnPage('/profile/' . $user->getId());
		
		$I->seeResponseCodeIs(200);
		
		$I->see($user->getProfile()->getBio(), 'p[class=text-black-50]');
		$I->see('Username - ' . $user->getUsername(), 'p[class=text-black-50]');
		$I->see('Full name - ' . $user->getFullName(), 'p[class=text-black-50]');
	}
}