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
 * @covers  \App\Controller\Comment\Restore
 * Class RestoreCest
 * @package App\Tests\functional\Comment
 * @author  Kristo Leas <kristo.leas@gmail.com>
 */
class RestoreCest
{
	use LoginConcern;
	use EntityManagerConcern;
	
	/**
	 * @param   FunctionalTester   $I
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function testRestore(FunctionalTester $I)
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
		
		/** @var Comment $comment */
		$comment = $em
			->createQueryBuilder()
			->select('c')
			->from('App\Entity\Comment', 'c')
			->where('c.author = :author')
			->andWhere('c.trashedAt IS NOT NULL')
			->setParameter('author', $user)
			->setMaxResults(1)
			->getQuery()
			->getSingleResult()
		;
		
		$I->amOnPage("/comments/{$comment->getId()}/restore");
		
		$I->seeResponseCodeIs(200);
		
		$I->see('Comment restored successfully.');
		
		$I->see($comment->getBody());
		
		/** @var Comment $comment */
		$comment = $I->grabEntityFromRepository(
			Comment::class,
			[
				'id' => $comment->getId(),
			]
		);
		
		$I->assertFalse($comment->isTrashed());
		
		$I->assertNull($comment->getTrashedAt());
		
		$I->assertNotNull($comment->getRestoredAt());
		
		$I->assertEquals(
			$user->getId(),
			$comment->getRestoredBy()->getId()
		);
	}
}