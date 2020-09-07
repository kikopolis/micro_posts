<?php

declare(strict_types = 1);

namespace App\Tests\functional\Concerns;

use App\Tests\AcceptanceTester;
use App\Tests\FunctionalTester;

/**
 * Trait LoginConcern
 * @package App\Tests\functional\Concerns
 */
trait LoginConcern
{
	/**
	 * @param  FunctionalTester|AcceptanceTester  $I
	 * @param  string                             $username
	 * @param  string                             $password
	 */
	protected function login($I, string $username = 'testUser', string $password = 'secret')
	{
		$I->amOnPage('/login');
		
		$I->seeResponseCodeIs(200);
		
		$I->seeLink('Login');
		$I->seeLink('Register');
		
		$I->dontSeeLink('Logout');
		
		$I->fillField('_username', $username);
		$I->fillField('_password', $password);
		
		$I->click('#Login');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		$I->see('Welcome back, Test User! You are now logged in!');
		
		$I->seeLink('Logout');
	}
}