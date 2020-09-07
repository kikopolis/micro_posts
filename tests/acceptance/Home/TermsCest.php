<?php

declare(strict_types = 1);

namespace App\Tests\acceptance\Home;

use App\Tests\AcceptanceTester;

/**
 * @covers  \App\Controller\Home\Terms
 * Class TermsCest
 * @package App\Tests\acceptance\Home
 */
class TermsCest
{
	/**
	 * @param  AcceptanceTester  $I
	 */
	public function termsPageWorks(AcceptanceTester $I)
	{
		$I->amOnPage('/terms-and-conditions');
		$I->see('No terms yet!', 'h1');
		$I->see('No posting either...', 'h4');
	}
}