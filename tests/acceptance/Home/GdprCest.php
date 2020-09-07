<?php

declare(strict_types = 1);

namespace App\Tests\acceptance\Home;

use App\Tests\AcceptanceTester;

/**
 * @covers  \App\Controller\Home\Gdpr
 * Class GdprCest
 * @package App\Tests\acceptance\Home
 */
class GdprCest
{
	/**
	 * @param  AcceptanceTester  $I
	 */
	public function gdprPageWorks(AcceptanceTester $I)
	{
		$I->amOnPage('/gdpr');
		$I->see('We do not sell your data!!!', 'h1');
	}
}