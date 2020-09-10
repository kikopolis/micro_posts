<?php

declare(strict_types = 1);

namespace App\Tests\functional\Comment;

use App\Entity\Comment;
use App\Entity\Notification\UserMentionedInCommentNotification;
use App\Entity\Post;
use App\Entity\User;
use App\Tests\functional\Concerns\EntityManagerConcern;
use App\Tests\functional\Concerns\LoginConcern;
use App\Tests\FunctionalTester;
use Codeception\Util\Fixtures;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @covers \App\Controller\Comment\Create
 * Class CreateCest
 * @package App\Tests\functional\Comment
 * @author  Kristo Leas <kristo.leas@gmail.com>
 */
class CreateCest
{
	use LoginConcern;
	use EntityManagerConcern;
	
	/**
	 * @param   FunctionalTester   $I
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function testCreate(FunctionalTester $I)
	{
		$body = 'This is a fine comment indeed!!!';
		
		$userData = Fixtures::get('activeTestUser');
		
		$this->login($I, $userData['username'], $userData['password']);
		
		$em = $this->getEm($I);
		
		/** @var Post $post */
		$post = $em
			->createQueryBuilder()
			->select('p')
			->from('App\Entity\Post', 'p')
			->setMaxResults(1)
			->getQuery()
			->getSingleResult()
		;
		
		$I->amOnPage("/posts/{$post->getId()}/show");
		
		$I->seeResponseCodeIs(200);
		
		$I->seeLink('Add a comment');
		
		$I->click('Add a comment');
		
		$I->seeResponseCodeIs(200);
		
		$I->fillField('comment[body]', $body);
		
		$I->click('comment[submit]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		$I->see($body);
		
		$I->see('Comment added! Before the public can view this comment, a moderator must approve it!');
	}
	
	/**
	 * @param   FunctionalTester   $I
	 * @throws NoResultException
	 * @throws NonUniqueResultException
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
		
		$body = "A fine comment about @{$randomUser->getUsername()} indeed!!!";
		
		$userData = Fixtures::get('activeTestUser');
		
		$this->login($I, $userData['username'], $userData['password']);
		
		$em = $this->getEm($I);
		
		/** @var Post $post */
		$post = $em
			->createQueryBuilder()
			->select('p')
			->from('App\Entity\Post', 'p')
			->setMaxResults(1)
			->getQuery()
			->getSingleResult()
		;
		
		$I->amOnPage("/posts/{$post->getId()}/show");
		
		$I->seeResponseCodeIs(200);
		
		$I->seeLink('Add a comment');
		
		$I->click('Add a comment');
		
		$I->seeResponseCodeIs(200);
		
		$I->fillField('comment[body]', $body);
		
		$I->click('comment[submit]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		$I->see($body);
		
		$I->see('Comment added! Before the public can view this comment, a moderator must approve it!');
		
		$I->seeInRepository(
			UserMentionedInCommentNotification::class,
			[
				'owner' => $randomUser,
			]
		);
	}
	
	/**
	 * @param   FunctionalTester   $I
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function testCreateStripsJavascript(FunctionalTester $I)
	{
		$body    = 'This is my first true functional comment creation test!!!';
		$script  = '<script>alert("malicious!!!");</script><script src="https://www.kikopolis.tech/malicious.js"></script>';
		$onClick = '<div onclick="alert(\'Malicious!!!\')" onfocus="alert(\'Malicious!!!\')">malicious div</div>';
		
		$userData = Fixtures::get('activeTestUser');
		
		$this->login($I, $userData['username'], $userData['password']);
		
		$em = $this->getEm($I);
		
		/** @var Post $post */
		$post = $em
			->createQueryBuilder()
			->select('p')
			->from('App\Entity\Post', 'p')
			->setMaxResults(1)
			->getQuery()
			->getSingleResult()
		;
		
		$I->amOnPage("/posts/{$post->getId()}/show");
		
		$I->seeResponseCodeIs(200);
		
		$I->seeLink('Add a comment');
		
		$I->click('Add a comment');
		
		$I->seeResponseCodeIs(200);
		
		$I->fillField('comment[body]', $body . $script . $onClick);
		
		$I->click('comment[submit]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		$I->see('Comment added! Before the public can view this comment, a moderator must approve it!');
		
		$I->see($body);
		$I->dontSeeInSource($script);
		$I->dontSeeInSource($onClick);
		$I->seeInSource('malicious div');
		$I->see('This is just a preview. For the public to see this comment, a moderator must approve this.');
	}
	
	/**
	 * @param   FunctionalTester   $I
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function testCreateStripsBadTags(FunctionalTester $I)
	{
		$body    = 'body';
		$badTags = '<div>bad tag</div><article>another bad tag</article>';
		
		$userData = Fixtures::get('activeTestUser');
		
		$this->login($I, $userData['username'], $userData['password']);
		
		$em = $this->getEm($I);
		
		/** @var Post $post */
		$post = $em
			->createQueryBuilder()
			->select('p')
			->from('App\Entity\Post', 'p')
			->setMaxResults(1)
			->getQuery()
			->getSingleResult()
		;
		
		$I->amOnPage("/posts/{$post->getId()}/show");
		
		$I->seeResponseCodeIs(200);
		
		$I->seeLink('Add a comment');
		
		$I->click('Add a comment');
		
		$I->seeResponseCodeIs(200);
		
		$I->fillField('comment[body]', $body . $badTags);
		
		$I->click('comment[submit]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		$I->see('Comment added! Before the public can view this comment, a moderator must approve it!');
		
		$I->see($body);
		$I->dontSeeInSource($badTags);
		$I->seeInSource('bad tag');
		$I->seeInSource('another bad tag');
		$I->see('This is just a preview. For the public to see this comment, a moderator must approve this.');
	}
	
	/**
	 * @param   FunctionalTester   $I
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function testCreateLeavesATag(FunctionalTester $I)
	{
		$body = 'This is my first true functional post creation test!!!';
		$aTag = '<a href="https://www.kikopolis.com">This is a link</a>';
		
		$userData = Fixtures::get('activeTestUser');
		
		$this->login($I, $userData['username'], $userData['password']);
		
		$em = $this->getEm($I);
		
		/** @var Post $post */
		$post = $em
			->createQueryBuilder()
			->select('p')
			->from('App\Entity\Post', 'p')
			->setMaxResults(1)
			->getQuery()
			->getSingleResult()
		;
		
		$I->amOnPage("/posts/{$post->getId()}/show");
		
		$I->seeResponseCodeIs(200);
		
		$I->seeLink('Add a comment');
		
		$I->click('Add a comment');
		
		$I->seeResponseCodeIs(200);
		
		$I->fillField('comment[body]', $body . $aTag);
		
		$I->click('comment[submit]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		$I->see('Comment added! Before the public can view this comment, a moderator must approve it!');
		
		$I->see($body);
		$I->seeInSource($aTag);
		$I->see('This is just a preview. For the public to see this comment, a moderator must approve this.');
	}
	
	/**
	 * @param   FunctionalTester   $I
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function testCreateLeavesImgTag(FunctionalTester $I)
	{
		$body   = 'This is my first true functional post creation test!!!';
		$imgTag = '<img src="https://www.kikopolis.com/image.jpg" alt="test image" />';
		
		$userData = Fixtures::get('activeTestUser');
		
		$this->login($I, $userData['username'], $userData['password']);
		
		$em = $this->getEm($I);
		
		/** @var Post $post */
		$post = $em
			->createQueryBuilder()
			->select('p')
			->from('App\Entity\Post', 'p')
			->setMaxResults(1)
			->getQuery()
			->getSingleResult()
		;
		
		$I->amOnPage("/posts/{$post->getId()}/show");
		
		$I->seeResponseCodeIs(200);
		
		$I->seeLink('Add a comment');
		
		$I->click('Add a comment');
		
		$I->seeResponseCodeIs(200);
		
		$I->fillField('comment[body]', $body . $imgTag);
		
		$I->click('comment[submit]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		$I->see('Comment added! Before the public can view this comment, a moderator must approve it!');
		
		$I->see($body);
		$I->seeInSource($imgTag);
		$I->see('This is just a preview. For the public to see this comment, a moderator must approve this.');
	}
}