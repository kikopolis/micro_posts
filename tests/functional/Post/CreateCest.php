<?php

declare(strict_types = 1);

namespace App\Tests\functional\Post;

use App\Entity\Notification\UserMentionedInPostNotification;
use App\Entity\User;
use App\Tests\functional\Concerns\LoginConcern;
use App\Tests\FunctionalTester;
use Codeception\Util\Fixtures;

/**
 * @covers  \App\Controller\Post\Create
 * Class CreateCest
 * @package App\Tests\functional\Post
 */
class CreateCest
{
	use LoginConcern;
	
	/**
	 * @param   FunctionalTester   $I
	 */
	public function testCreate(FunctionalTester $I)
	{
		$body = 'This is my first true functional post creation test!!!';
		
		$userData = Fixtures::get('activeTestUser');
		
		$this->login($I, $userData['username'], $userData['password']);
		
		$I->amOnPage('/posts/create');
		
		$I->seeResponseCodeIs(200);
		
		$I->fillField('post[body]', $body);
		
		$I->click('post[submit]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		$I->see($body);
		$I->see('Microscopic Post with the id of ');
		$I->see('This is just a preview. For the public to see this post, a moderator must approve this.');
	}
	
	/**
	 * @param   FunctionalTester   $I
	 */
	public function testCreateMarksAndNotifiesMentioned(FunctionalTester $I)
	{
		/** @var User $randomUser */
		$randomUser = $I->grabEntityFromRepository(
			User::class,
			[
				'id' => 2,
			]
		);
		
		$body = "This is my first true functional post creation test!!! @{$randomUser->getUsername()}";
		
		$userData = Fixtures::get('activeTestUser');
		
		$this->login($I, $userData['username'], $userData['password']);
		
		$I->amOnPage('/posts/create');
		
		$I->seeResponseCodeIs(200);
		
		$I->fillField('post[body]', $body);
		
		$I->click('post[submit]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		$I->see($body);
		$I->see('Microscopic Post with the id of ');
		$I->see('This is just a preview. For the public to see this post, a moderator must approve this.');
		
		$I->seeInRepository(
			UserMentionedInPostNotification::class,
			[
				'owner' => $randomUser,
			]
		);
	}
	
	/**
	 * @param   FunctionalTester   $I
	 */
	public function testCreateStripsJavascript(FunctionalTester $I)
	{
		$body    = 'This is my first true functional post creation test!!!';
		$script  = '<script>alert("malicious!!!");</script><script src="https://www.kikopolis.tech/malicious.js"></script>';
		$onClick = '<div onclick="alert(\'Malicious!!!\')" onfocus="alert(\'Malicious!!!\')">malicious div</div>';
		
		$userData = Fixtures::get('activeTestUser');
		
		$this->login($I, $userData['username'], $userData['password']);
		
		$I->amOnPage('/posts/create');
		
		$I->seeResponseCodeIs(200);
		
		$I->fillField('post[body]', $body . $script . $onClick);
		
		$I->click('post[submit]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		$I->see($body);
		$I->dontSeeInSource($script);
		$I->dontSeeInSource($onClick);
		$I->seeInSource('malicious div');
		$I->see('Microscopic Post with the id of ');
		$I->see('This is just a preview. For the public to see this post, a moderator must approve this.');
	}
	
	/**
	 * @param   FunctionalTester   $I
	 */
	public function testCreateStripsBadTags(FunctionalTester $I)
	{
		$body    = 'body';
		$badTags = '<div>bad tag</div><article>another bad tag</article>';
		
		$userData = Fixtures::get('activeTestUser');
		
		$this->login($I, $userData['username'], $userData['password']);
		
		$I->amOnPage('/posts/create');
		
		$I->seeResponseCodeIs(200);
		
		$I->fillField('post[body]', $body . $badTags);
		
		$I->click('post[submit]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		$I->see($body);
		$I->dontSeeInSource($badTags);
		$I->seeInSource('bad tag');
		$I->seeInSource('another bad tag');
		$I->see('Microscopic Post with the id of ');
		$I->see('This is just a preview. For the public to see this post, a moderator must approve this.');
	}
	
	/**
	 * @param   FunctionalTester   $I
	 */
	public function testCreateLeavesATag(FunctionalTester $I)
	{
		$body = 'This is my first true functional post creation test!!!';
		$aTag = '<a href="https://www.kikopolis.com">This is a link</a>';
		
		$userData = Fixtures::get('activeTestUser');
		
		$this->login($I, $userData['username'], $userData['password']);
		
		$I->amOnPage('/posts/create');
		
		$I->seeResponseCodeIs(200);
		
		$I->fillField('post[body]', $body . $aTag);
		
		$I->click('post[submit]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		$I->see($body);
		$I->seeInSource($aTag);
		$I->see('Microscopic Post with the id of ');
		$I->see('This is just a preview. For the public to see this post, a moderator must approve this.');
	}
	
	/**
	 * @param   FunctionalTester   $I
	 */
	public function testCreateLeavesImgTag(FunctionalTester $I)
	{
		$body   = 'This is my first true functional post creation test!!!';
		$imgTag = '<img src="https://www.kikopolis.com/image.jpg" alt="test image" />';
		
		$userData = Fixtures::get('activeTestUser');
		
		$this->login($I, $userData['username'], $userData['password']);
		
		$I->amOnPage('/posts/create');
		
		$I->seeResponseCodeIs(200);
		
		$I->fillField('post[body]', $body . $imgTag);
		
		$I->click('post[submit]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		$I->see($body);
		$I->seeInSource($imgTag);
		$I->see('Microscopic Post with the id of ');
		$I->see('This is just a preview. For the public to see this post, a moderator must approve this.');
	}
}