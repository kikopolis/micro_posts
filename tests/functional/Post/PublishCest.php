<?php

declare(strict_types = 1);

namespace App\Tests\functional\Post;

use App\Entity\Post;
use App\Entity\User;
use App\Tests\functional\Concerns\EntityManagerConcern;
use App\Tests\functional\Concerns\LoginConcern;
use App\Tests\FunctionalTester;
use Codeception\Util\Fixtures;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @covers  \App\Controller\Post\Publish
 * Class PublishCest
 * @package App\Tests\functional\Post
 */
class PublishCest
{
	use LoginConcern;
	use EntityManagerConcern;
	
	/**
	 * @param  FunctionalTester  $I
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function testPublish(FunctionalTester $I)
	{
		$userData = Fixtures::get('activeTestUser');
		
		$this->login($I, $userData['username'], $userData['password']);
		
		/** @var User $user */
		$user = $I->grabEntityFromRepository(
			User::class,
			[
				'username' => $userData['username'],
			]
		);
		
		$em = $this->getEmWithoutFilters($I);
		
		/** @var Post $post */
		$post = $em
			->createQueryBuilder()
			->select('p')
			->from('App\Entity\Post', 'p')
			->where('p.author = :author')
			->andWhere('p.publishedAt IS NULL')
			->setParameter('author', $user)
			->setMaxResults(1)
			->getQuery()
			->getSingleResult()
		;
		
		$I->amOnPage("/posts/{$post->getId()}/publish");
		
		$I->seeResponseCodeIs(200);
		
		$I->see('Post is successfully published! If a moderator has approved it, it is now public!');
		
		$I->see($post->getBody());
		
		/** @var Post $post */
		$post = $I->grabEntityFromRepository(
			Post::class,
			[
				'id' => $post->getId(),
			]
		);
		
		$I->assertTrue($post->isPublished());
		
		$I->assertNotNull($post->getPublishedAt());
		
		$I->assertEquals(
			$user->getId(),
			$post->getPublishedBy()->getId()
		);
	}
}