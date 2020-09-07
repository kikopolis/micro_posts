<?php

declare(strict_types = 1);

namespace App\Tests\acceptance\Home;

use App\Tests\AcceptanceTester;

/**
 * @covers  \App\Controller\Home\Support
 * Class HelpCest
 * @package App\Tests\acceptance\Home
 */
class SupportCest
{
	/**
	 * @param  AcceptanceTester  $I
	 */
	public function supportPageWorks(AcceptanceTester $I)
	{
		$I->amOnPage('/support');
		$I->see('Registration issues', 'h2');
		$I->see('I have not received a registration confirmation token!', 'h4');
	}
}