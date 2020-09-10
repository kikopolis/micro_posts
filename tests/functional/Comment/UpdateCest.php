<?php

declare(strict_types = 1);

namespace App\Tests\functional\Comment;

use App\Controller\Concerns\DisableDoctrineFiltersConcern;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Tests\functional\Concerns\EntityManagerConcern;
use App\Tests\functional\Concerns\LoginConcern;
use App\Tests\FunctionalTester;
use Codeception\Util\Fixtures;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @covers  \App\Controller\Comment\Update
 * Class UpdateCest
 * @package App\Tests\functional\Comment
 * @author  Kristo Leas <kristo.leas@gmail.com>
 */
class UpdateCest
{
	use DisableDoctrineFiltersConcern;
	use EntityManagerConcern;
	use LoginConcern;
	
	/**
	 * @param   FunctionalTester   $I
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function testUpdatingApprovedComment(FunctionalTester $I)
	{
		$userData = Fixtures::get('activeTestUser');
		
		$this->login($I, $userData['username'], $userData['password']);
		
		$em = $this->getEm($I);
		
		/** @var User $user */
		$user = $I->grabEntityFromRepository(
			User::class,
			[
				'username' => $userData['username'],
			]
		);
		
		/** @var Comment $comment */
		$comment = $em->createQueryBuilder()
		              ->select('c')
		              ->from('App\Entity\Comment', 'c')
		              ->where('c.author = :author')
		              ->setParameter('author', $user)
		              ->setMaxResults(1)
		              ->getQuery()
		              ->getSingleResult()
		;
		
		$body = 'This is an edited comment! Edit is successful!';
		
		$I->amOnPage("/comments/{$comment->getId()}/edit");
		
		$I->seeResponseCodeIs(200);
		
		$I->fillField('comment[body]', $body);
		
		$I->click('comment[submit]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		$I->see($body);
		$I->see('This is just a preview. For the public to see this comment, a moderator must approve this.');
	}
	
	/**
	 * @param   FunctionalTester   $I
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function testUpdatingUnApprovedComment(FunctionalTester $I)
	{
		$userData = Fixtures::get('activeTestUser');
		
		$this->login($I, $userData['username'], $userData['password']);
		
		$em = $this->getEmWithoutFilters($I);
		
		/** @var User $user */
		$user = $I->grabEntityFromRepository(
			User::class,
			[
				'username' => $userData['username'],
			]
		);
		
		/** @var Comment $comment */
		$comment = $em->createQueryBuilder()
		              ->select('c')
		              ->from('App\Entity\Comment', 'c')
		              ->where('c.author = :author')
		              ->andWhere('c.approvedAt IS NULL')
		              ->setParameter('author', $user)
		              ->setMaxResults(1)
		              ->getQuery()
		              ->getSingleResult()
		;
		
		$body = 'This is an edited comment! Edit is successful!';
		
		$I->amOnPage("/comments/{$comment->getId()}/edit");
		
		$I->seeResponseCodeIs(200);
		
		$I->fillField('comment[body]', $body);
		
		$I->click('comment[submit]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		$I->see($body);
		$I->see('This is just a preview. For the public to see this comment, a moderator must approve this.');
	}
	
	/**
	 * @param   FunctionalTester   $I
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function testUpdatingReportedComment(FunctionalTester $I)
	{
		$userData = Fixtures::get('activeTestUser');
		
		$this->login($I, $userData['username'], $userData['password']);
		
		$em = $this->getEm($I);
		
		/** @var User $user */
		$user = $I->grabEntityFromRepository(
			User::class,
			[
				'username' => $userData['username'],
			]
		);
		
		/** @var Comment $comment */
		$comment = $em->createQueryBuilder()
		              ->select('c')
		              ->from('App\Entity\Comment', 'c')
		              ->where('c.author = :author')
		              ->andWhere('c.reported = true')
		              ->setParameter('author', $user)
		              ->setMaxResults(1)
		              ->getQuery()
		              ->getSingleResult()
		;
		
		$body = 'This is an edited comment! Edit is successful!';
		
		$I->amOnPage("/comments/{$comment->getId()}/edit");
		
		$I->seeResponseCodeIs(200);
		
		$I->fillField('comment[body]', $body);
		
		$I->click('comment[submit]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		$I->see($body);
		$I->see('This is just a preview. For the public to see this comment, a moderator must approve this.');
	}
	
	/**
	 * @param   FunctionalTester   $I
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function testUpdatingTrashedComment(FunctionalTester $I)
	{
		$userData = Fixtures::get('activeTestUser');
		
		$this->login($I, $userData['username'], $userData['password']);
		
		$em = $this->getEmWithoutFilters($I);
		
		/** @var User $user */
		$user = $I->grabEntityFromRepository(
			User::class,
			[
				'username' => $userData['username'],
			]
		);
		
		/** @var Comment $comment */
		$comment = $em->createQueryBuilder()
		              ->select('c')
		              ->from('App\Entity\Comment', 'c')
		              ->where('c.author = :author')
		              ->andWhere('c.trashedAt IS NOT NULL')
		              ->setParameter('author', $user)
		              ->setMaxResults(1)
		              ->getQuery()
		              ->getSingleResult()
		;
		
		$body = 'This is an edited comment! Edit is successful!';
		
		$I->amOnPage("/comments/{$comment->getId()}/edit");
		
		$I->seeResponseCodeIs(200);
		
		$I->fillField('comment[body]', $body);
		
		$I->click('comment[submit]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		$I->see($body);
		$I->see('This is just a preview. For the public to see this comment, a moderator must approve this.');
	}
	
	/**
	 * @param   FunctionalTester   $I
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function testUpdatingBrandNewComment(FunctionalTester $I)
	{
		$body = 'This is a fine comment indeed!!!';
		
		$userData = Fixtures::get('activeTestUser');
		
		$this->login($I, $userData['username'], $userData['password']);
		
		$em = $this->getEmWithoutFilters($I);
		
		/** @var Post $post */
		$post = $em
			->createQueryBuilder()
			->select('p')
			->from('App\Entity\Post', 'p')
			->where('p.publishedAt IS NOT NULL')
			->andWhere('p.trashedAt IS NULL')
			->andWhere('p.approvedAt IS NOT NULL')
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
		
		/** @var Comment $comment */
		$comment = $em->createQueryBuilder()
		              ->select('c')
		              ->from('App\Entity\Comment', 'c')
		              ->where('c.body = :body')
		              ->setParameter('body', $body)
		              ->getQuery()
		              ->getSingleResult()
		;
		
		$body = 'This is an edited comment! Edit is successful!';
		
		$I->amOnPage("/comments/{$comment->getId()}/edit");
		
		$I->seeResponseCodeIs(200);
		
		$I->fillField('comment[body]', $body);
		
		$I->click('comment[submit]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		$I->see($body);
		$I->see('This is just a preview. For the public to see this comment, a moderator must approve this.');
	}
}