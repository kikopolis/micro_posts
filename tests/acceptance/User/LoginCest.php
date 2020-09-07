<?php

declare(strict_types = 1);

namespace App\Tests\acceptance\User;

use App\Tests\AcceptanceTester;

/**
 * @covers  \App\Controller\User\Login
 * Class LoginCest
 * @package App\Tests\acceptance\User
 */
class LoginCest
{
	/**
	 * @param  AcceptanceTester  $I
	 */
	public function loginWorks(AcceptanceTester $I)
	{
		$I->amOnPage('/login');
		$I->seeLink('Login');
		$I->seeLink('Register');
		$I->dontSeeLink('Logout');
		$I->fillField('_username', 'testUser');
		$I->fillField('_password', 'secret');
		$I->dontSeeCheckboxIsChecked('_remember_me');
		$I->checkOption('_remember_me');
		$I->click('#Login');
		$I->expect('form is submitted correctly');
		$I->see('Welcome back, Test User! You are now logged in!');
		$I->seeLink('Logout');
	}
}