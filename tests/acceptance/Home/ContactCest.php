<?php

declare(strict_types = 1);

namespace App\Tests\acceptance\Home;

use App\Tests\AcceptanceTester;

/**
 * @covers \App\Controller\Home\Contact
 * Class ContactCest
 * @package App\Tests\acceptance\Home
 */
class ContactCest
{
	/**
	 * @param  AcceptanceTester  $I
	 */
	public function contactPageWorks(AcceptanceTester $I)
	{
		$I->amOnPage('/contact');
		$I->see('Dont write to meee!!!!', 'h1');
	}
}