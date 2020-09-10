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
 * @covers  \App\Controller\Comment\Preview
 * Class PreviewCest
 * @package App\Tests\functional\Comment
 * @author  Kristo Leas <kristo.leas@gmail.com>
 */
class PreviewCest
{
	use LoginConcern;
	use EntityManagerConcern;
	
	/**
	 * @param   FunctionalTester   $I
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function testPreview(FunctionalTester $I)
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
			->andWhere('c.approvedAt IS NULL')
			->setParameter('author', $user)
			->setMaxResults(1)
			->getQuery()
			->getSingleResult()
		;
		
		$I->amOnPage("/comments/{$comment->getId()}/preview");
		
		$I->seeResponseCodeIs(200);
		
		$I->see($comment->getBody());
	}
}