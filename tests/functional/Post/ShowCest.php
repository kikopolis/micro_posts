<?php

declare(strict_types = 1);

namespace App\Tests\functional\Post;

use App\Entity\Post;
use App\Tests\functional\Concerns\EntityManagerConcern;
use App\Tests\FunctionalTester;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @covers  \App\Controller\Post\Show
 * Class ShowCest
 * @package App\Tests\functional\Post
 */
class ShowCest
{
	use EntityManagerConcern;
	
	/**
	 * @param  FunctionalTester  $I
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function testShow(FunctionalTester $I)
	{
		$em = $this->getEm($I);
		
		/** @var Post $post */
		$post = $em
			->createQueryBuilder()
			->select('p')
			->from('App\Entity\Post', 'p')
			->where('p.trashedAt IS NULL')
			->andWhere('p.publishedAt IS NOT NULL')
			->andWhere('p.approvedAt IS NOT NULL')
			->setMaxResults(1)
			->getQuery()
			->getSingleResult()
		;
		
		$I->amOnPage("/posts/{$post->getId()}/show");
		
		$I->seeResponseCodeIs(200);
		
		$I->see($post->getBody());
	}
	
	/**
	 * @param  FunctionalTester  $I
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function testNoApprovedNoShow(FunctionalTester $I)
	{
		$em = $this->getEmWithoutFilters($I);
		
		/** @var Post $post */
		$post = $em
			->createQueryBuilder()
			->select('p')
			->from('App\Entity\Post', 'p')
			->where('p.approvedAt IS NULL')
			->setMaxResults(1)
			->getQuery()
			->getSingleResult()
		;
		
		$I->amOnPage("/posts/{$post->getId()}/show");
		
		$I->seeResponseCodeIs(404);
		
		$I->dontSee($post->getBody());
		
		$I->see('No post found. Try a different one.');
	}
	
	/**
	 * @param  FunctionalTester  $I
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function testNoPublishedNoShow(FunctionalTester $I)
	{
		$em = $this->getEmWithoutFilters($I);
		
		/** @var Post $post */
		$post = $em
			->createQueryBuilder()
			->select('p')
			->from('App\Entity\Post', 'p')
			->where('p.publishedAt IS NULL')
			->setMaxResults(1)
			->getQuery()
			->getSingleResult()
		;
		
		$I->amOnPage("/posts/{$post->getId()}/show");
		
		$I->seeResponseCodeIs(404);
		
		$I->dontSee($post->getBody());
		
		$I->see('No post found. Try a different one.');
	}
	
	/**
	 * @param  FunctionalTester  $I
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function testTrashedNoShow(FunctionalTester $I)
	{
		$em = $this->getEmWithoutFilters($I);
		
		/** @var Post $post */
		$post = $em
			->createQueryBuilder()
			->select('p')
			->from('App\Entity\Post', 'p')
			->where('p.trashedAt IS NOT NULL')
			->setMaxResults(1)
			->getQuery()
			->getSingleResult()
		;
		
		$I->amOnPage("/posts/{$post->getId()}/show");
		
		$I->seeResponseCodeIs(404);
		
		$I->dontSee($post->getBody());
		
		$I->see('No post found. Try a different one.');
	}
}