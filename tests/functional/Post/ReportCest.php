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
 * Class ReportCest
 * @package App\Tests\functional\Post
 */
class ReportCest
{
	use LoginConcern;
	use EntityManagerConcern;
	
	/**
	 * @param  FunctionalTester  $I
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function testReport(FunctionalTester $I)
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
		
		$em = $this->getEm($I);
		
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
		
		$I->assertEquals(
			0,
			$post->getLikedBy()->count()
		);
		
		$I->amOnPage("/posts/{$post->getId()}/report");
		
		$I->seeResponseCodeIs(200);
		
		$I->see('Post reported as inappropriate. A mod will review it asap.');
		
		/** @var Post $post */
		$post = $I->grabEntityFromRepository(
			Post::class,
			[
				'id' => $post->getId(),
			]
		);
		
		$I->assertEquals(
			1,
			$post->getReportedBy()->count()
		);
		
		$I->assertTrue($post->isReported());
		
		$I->assertTrue(
			$post->getReportedBy()->exists(
				fn(int $key, User $element): bool => $element->getId() === $user->getId()
			)
		);
	}
}