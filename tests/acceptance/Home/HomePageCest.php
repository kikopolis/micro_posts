<?php

declare(strict_types = 1);

namespace App\Tests\acceptance\Home;

use App\Tests\AcceptanceTester;

/**
 * @covers \App\Controller\Home\Index
 * Class HomePageCest
 * @package App\Tests\acceptance\Home
 */
class HomePageCest
{
	/**
	 * @param  AcceptanceTester  $I
	 */
	public function homePageWorks(AcceptanceTester $I)
	{
		$I->amOnPage('/');
		$I->see('Welcome to kikopolis.tech MicroPost platform!!', 'h5');
	}
}