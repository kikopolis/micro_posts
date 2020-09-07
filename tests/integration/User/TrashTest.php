<?php

declare(strict_types = 1);

namespace App\Tests\integration\User;

use App\Entity\User;
use App\Event\User\TrashEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use App\Tests\IntegrationTester;
use Codeception\Test\Unit;

/**
 * @coversNothing
 * Class TrashTest
 * @package App\Tests\integration\User
 */
class TrashTest extends Unit
{
	use EventDispatcherConcern;
	use EntityManagerConcern;
	
	/**
	 * @var IntegrationTester
	 */
	protected IntegrationTester $tester;
	
	public function testTrash()
	{
		$ed = $this->getEd();
		$em = $this->getEm();
		
		/** @var User $user */
		$user = $em->find(User::class, 1);
		
		
		$ed->dispatch(
			new TrashEvent($user, $user)
		);
		
		$em->flush();
		
		// assertions
		static::assertTrue($user->isTrashed());
		static::assertNotNull($user->getTrashedAt());
		static::assertEquals(
			$user->getId(),
			$user->getTrashedBy()->getId()
		);
	}
}