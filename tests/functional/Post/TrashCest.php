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
 * @covers  \App\Controller\Post\Trash
 * Class TrashCest
 * @package App\Tests\functional\Post
 */
class TrashCest
{
	use LoginConcern;
	use EntityManagerConcern;
	
	/**
	 * @param  FunctionalTester  $I
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function testTrash(FunctionalTester $I)
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
			->andWhere('p.publishedAt IS NOT NULL')
			->andWhere('p.approvedAt IS NOT NULL')
			->andWhere('p.trashedAt IS NULL')
			->setParameter('author', $user)
			->setMaxResults(1)
			->getQuery()
			->getSingleResult()
		;
		
		$I->amOnPage("/posts/{$post->getId()}/trash");
		
		$I->seeResponseCodeIs(200);
		
		/** @var Post $post */
		$post = $em->find(Post::class, $post->getId());
		
		$I->assertTrue($post->isTrashed());
		
		$I->assertNotNull($post->getTrashedAt());
		
		$I->assertNull($post->getRestoredAt());
		
		$I->assertNull($post->getRestoredBy());
		
		$I->assertEquals(
			$user->getId(),
			$post->getTrashedBy()->getId()
		);
	}
}