<?php

declare(strict_types = 1);

namespace App\Tests\functional\Comment;

use App\Entity\Comment;
use App\Entity\User;
use App\Tests\functional\Concerns\EntityManagerConcern;
use App\Tests\functional\Concerns\LoginConcern;
use App\Tests\FunctionalTester;
use Codeception\Util\Fixtures;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @covers  \App\Controller\Comment\Like
 * Class LikeCest
 * @package App\Tests\functional\Comment
 * @author  Kristo Leas <kristo.leas@gmail.com>
 */
class LikeCest
{
	use LoginConcern;
	use EntityManagerConcern;
	
	/**
	 * @param   FunctionalTester   $I
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
		
		/** @var Comment $comment */
		$comment = $em
			->createQueryBuilder()
			->select('c')
			->from('App\Entity\Comment', 'c')
			->where('c.author != :author')
			->setParameter('author', $user)
			->setMaxResults(1)
			->getQuery()
			->getSingleResult()
		;
		
		$I->assertFalse(
			$comment->getLikedBy()->contains($user)
		);
		
		$I->assertEquals(
			0,
			$comment->getLikedBy()->count()
		);
		
		$I->amOnPage("/comments/{$comment->getId()}/like");
		
		$I->seeResponseCodeIs(200);
		
		/** @var Comment $comment */
		$comment = $I->grabEntityFromRepository(
			Comment::class,
			[
				'id' => $comment->getId(),
			]
		);
		
		$I->assertEquals(
			1,
			$comment->getLikedBy()->count()
		);
		
		$I->assertTrue(
			$comment->getLikedBy()->exists(
				fn(int $key, User $element): bool => $element->getId() === $user->getId()
			)
		);
		
		$I->see('Like added successfully!');
		
		$I->see($comment->getBody());
	}
}