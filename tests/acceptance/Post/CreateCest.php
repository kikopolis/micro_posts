<?php

declare(strict_types = 1);

namespace App\Tests\acceptance\Post;

use App\Tests\AcceptanceTester;
use App\Tests\_services\Sanitizer\SanitizerService;
use Faker\Factory;

/**
 * @covers  \App\Controller\Post\Create
 * Class CreateCest
 * @package App\Tests\acceptance\Post
 */
class CreateCest
{
	/**
	 * @param  AcceptanceTester  $I
	 */
	public function postingNewPostWorks(AcceptanceTester $I)
	{
		$body = implode(' ', Factory::create()->words(20));
		
		$sanitizer = new SanitizerService();
		
		$clean = $sanitizer->cleanse($body);
		
		$I->amOnPage('/login');
		$I->fillField('_username', 'testUser');
		$I->fillField('_password', 'secret');
		$I->checkOption('_remember_me');
		$I->click('#Login');
		$I->expect('form is submitted correctly');
		$I->see('Welcome back, Test User! You are now logged in!');
		$I->seeLink('Logout');
		$I->amOnPage('/');
		$I->seeLink('New');
		$I->click('New');
		$I->seeLink('Read them here');
		$I->see('Maximum of 280 characters!', 'h4');
		$I->see("Don't forget to browse our terms of service and the rules for posting!!", 'h5');
		$I->see('0', '#characterCount');
		$I->fillField('post[body]', $body);
		$I->click('#post_submit');
		$I->expect('form is submitted correctly');
		// note the post is un-approved upon changes, so it redirects instead to post index
		
		$I->see($clean, 'p[class=text-dark]');
		$I->see('Microscopic Post with the id of', 'h1');
		$I->seeLink('@testUser');
		$I->seeLink('Edit');
		$I->seeLink('Trash');
		$I->dontSeeLink('Report');
		$I->dontSeeLink('Like!');
		$I->dontSeeLink('Inappropriate post');
		$I->seeLink('Add a comment');
		$I->seeLink('No comments yet!');
	}
	
}