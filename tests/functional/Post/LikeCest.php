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
 * @covers  \App\Controller\Post\Like
 * Class LikeCest
 * @package App\Tests\functional\Post
 */
class LikeCest
{
	use LoginConcern;
	use EntityManagerConcern;
	
	/**
	 * @param  FunctionalTester  $I
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function testLike(FunctionalTester $I)
	{
		$userData = Fixtures::get('activeTestUser');
		
		$em = $this->getEm($I);
		
		$this->login($I, $userData['username'], $userData['password']);
		
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
			->where('p.author != :author')
			->setParameter('author', $user)
			->setMaxResults(1)
			->getQuery()
			->getSingleResult()
		;
		
		$I->assertFalse(
			$post->getLikedBy()->contains($user)
		);
		
		$I->assertEquals(
			0,
			$post->getLikedBy()->count()
		);
		
		$I->amOnPage("/posts/{$post->getId()}/like");
		
		$I->seeResponseCodeIs(200);
		
		/** @var Post $post */
		$post = $I->grabEntityFromRepository(
			Post::class,
			[
				'id' => $post->getId(),
			]
		);
		
		$I->assertEquals(
			1,
			$post->getLikedBy()->count()
		);
		
		$I->assertTrue(
			$post->getLikedBy()->exists(
				fn(int $key, User $element): bool => $element->getId() === $user->getId()
			)
		);
		
		$I->see('Like added successfully!');
		
		$I->see($post->getBody());
	}
}