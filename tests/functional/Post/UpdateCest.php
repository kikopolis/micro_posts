<?php

declare(strict_types = 1);

namespace App\Tests\functional\Post;

use App\Controller\Concerns\DisableDoctrineFiltersConcern;
use App\Entity\Post;
use App\Entity\User;
use App\Tests\functional\Concerns\EntityManagerConcern;
use App\Tests\functional\Concerns\LoginConcern;
use App\Tests\FunctionalTester;
use Codeception\Util\Fixtures;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @covers \App\Controller\Post\Update
 * Class UpdateCest
 * @package App\Tests\functional\Post
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
	public function testUpdatingPublishedAndApprovedPost(FunctionalTester $I)
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
		
		/** @var Post $post */
		$post = $em
			->createQueryBuilder()
			->select('p')
			->from('App\Entity\Post', 'p')
			->where('p.author = :author')
			->setParameter('author', $user)
			->setMaxResults(1)
			->getQuery()
			->getSingleResult()
		;
		
		$body = 'This is an edited post! Edit is successful!';
		
		$I->amOnPage("/posts/{$post->getId()}/show");
		
		$I->seeResponseCodeIs(200);
		
		$I->see($post->getBody());
		
		$I->seeLink('Edit');
		
		$I->click('Edit');
		
		$I->seeResponseCodeIs(200);
		
		$I->fillField('post[body]', $body);
		
		$I->click('post[submit]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		$I->see($body);
		$I->see('Microscopic Post with the id of ');
		$I->see('This is just a preview. For the public to see this post, a moderator must approve this.');
		
		$I->amOnPage("/posts/{$post->getId()}/show");
		
		$I->seeResponseCodeIs(404);
	}
	
	/**
	 * @param   FunctionalTester   $I
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function testUpdatingUnPublishedPost(FunctionalTester $I)
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
		
		/** @var Post $post */
		$post = $em
			->createQueryBuilder()
			->select('p')
			->from('App\Entity\Post', 'p')
			->where('p.publishedAt IS NULL')
			->andWhere('p.author = :author')
			->setParameter('author', $user)
			->setMaxResults(1)
			->getQuery()
			->getSingleResult()
		;
		
		$body = 'This is an edited post! Edit is successful!';
		
		$I->amOnPage("/posts/{$post->getId()}/show");
		
		$I->seeResponseCodeIs(404);
		
		$I->amOnPage("/posts/{$post->getId()}/preview");
		
		$I->seeResponseCodeIs(200);
		
		$I->see($post->getBody());
		
		$I->seeLink('Edit');
		
		$I->click('Edit');
		
		$I->seeResponseCodeIs(200);
		
		$I->fillField('post[body]', $body);
		
		$I->click('post[submit]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		$I->see($body);
		$I->see('Microscopic Post with the id of ');
		$I->see('This is just a preview. For the public to see this post, a moderator must approve this.');
		
		$I->amOnPage("/posts/{$post->getId()}/show");
		
		$I->seeResponseCodeIs(404);
	}
	
	/**
	 * @param   FunctionalTester   $I
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function testUpdatingUnApprovedPost(FunctionalTester $I)
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
		
		/** @var Post $post */
		$post = $em
			->createQueryBuilder()
			->select('p')
			->from('App\Entity\Post', 'p')
			->where('p.approvedAt IS NULL')
			->andWhere('p.author = :author')
			->setParameter('author', $user)
			->setMaxResults(1)
			->getQuery()
			->getSingleResult()
		;
		
		$body = 'This is an edited post! Edit is successful!';
		
		$I->amOnPage("/posts/{$post->getId()}/show");
		
		$I->seeResponseCodeIs(404);
		
		$I->amOnPage("/posts/{$post->getId()}/preview");
		
		$I->seeResponseCodeIs(200);
		
		$I->see($post->getBody());
		
		$I->seeLink('Edit');
		
		$I->click('Edit');
		
		$I->seeResponseCodeIs(200);
		
		$I->fillField('post[body]', $body);
		
		$I->click('post[submit]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		$I->see($body);
		$I->see('Microscopic Post with the id of ');
		$I->see('This is just a preview. For the public to see this post, a moderator must approve this.');
		
		$I->amOnPage("/posts/{$post->getId()}/show");
		
		$I->seeResponseCodeIs(404);
	}
	
	/**
	 * @param   FunctionalTester   $I
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function testUpdatingReportedPost(FunctionalTester $I)
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
		
		/** @var Post $post */
		$post = $em
			->createQueryBuilder()
			->select('p')
			->from('App\Entity\Post', 'p')
			->where('p.reported = true')
			->andWhere('p.author = :author')
			->setParameter('author', $user)
			->setMaxResults(1)
			->getQuery()
			->getSingleResult()
		;
		
		$body = 'This is an edited post! Edit is successful!';
		
		$I->amOnPage("/posts/{$post->getId()}/show");
		
		$I->seeResponseCodeIs(200);
		
		$I->see($post->getBody());
		
		$I->seeLink('Edit');
		
		$I->click('Edit');
		
		$I->seeResponseCodeIs(200);
		
		$I->fillField('post[body]', $body);
		
		$I->click('post[submit]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		$I->see($body);
		$I->see('Microscopic Post with the id of ');
		$I->see('This is just a preview. For the public to see this post, a moderator must approve this.');
		
		$I->amOnPage("/posts/{$post->getId()}/show");
		
		$I->seeResponseCodeIs(404);
	}
	
	/**
	 * @param   FunctionalTester   $I
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function testUpdatingTrashedPost(FunctionalTester $I)
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
		
		/** @var Post $post */
		$post = $em
			->createQueryBuilder()
			->select('p')
			->from('App\Entity\Post', 'p')
			->where('p.trashedAt IS NOT NULL')
			->andWhere('p.author = :author')
			->setParameter('author', $user)
			->setMaxResults(1)
			->getQuery()
			->getSingleResult()
		;
		
		$body = 'This is an edited post! Edit is successful!';
		
		$I->amOnPage("/posts/{$post->getId()}/show");
		
		$I->seeResponseCodeIs(404);
		
		$I->amOnPage("/posts/{$post->getId()}/preview");
		
		$I->seeResponseCodeIs(200);
		
		$I->see($post->getBody());
		
		$I->seeLink('Edit');
		
		$I->click('Edit');
		
		$I->seeResponseCodeIs(200);
		
		$I->fillField('post[body]', $body);
		
		$I->click('post[submit]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		$I->see($body);
		$I->see('Microscopic Post with the id of ');
		$I->see('This is just a preview. For the public to see this post, a moderator must approve this.');
		
		$I->amOnPage("/posts/{$post->getId()}/show");
		
		$I->seeResponseCodeIs(404);
	}
	
	/**
	 * @param   FunctionalTester   $I
	 */
	public function testUpdatingBrandNewPost(FunctionalTester $I)
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
		
		/** @var Post $post */
		$post = $I->grabEntityFromRepository(
			Post::class,
			[
				'body' => $body,
			]
		);
		
		$body = 'This is an edited post! Edit is successful!';
		
		$I->amOnPage("/posts/{$post->getId()}/show");
		
		$I->seeResponseCodeIs(404);
		
		$I->amOnPage("/posts/{$post->getId()}/preview");
		
		$I->seeResponseCodeIs(200);
		
		$I->see($post->getBody());
		
		$I->seeLink('Edit');
		
		$I->click('Edit');
		
		$I->seeResponseCodeIs(200);
		
		$I->fillField('post[body]', $body);
		
		$I->click('post[submit]');
		
		$I->expect('form is submitted correctly');
		
		$I->seeResponseCodeIs(200);
		
		$I->see($body);
		$I->see('Microscopic Post with the id of ');
		$I->see('This is just a preview. For the public to see this post, a moderator must approve this.');
		
		$I->amOnPage("/posts/{$post->getId()}/show");
		
		$I->seeResponseCodeIs(404);
	}
}