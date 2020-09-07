<?php

declare(strict_types = 1);

namespace App\Tests\integration\User;

use App\Entity\Complaint;
use App\Entity\User;
use App\Event\AuthorableCreatedEvent;
use App\Event\Complaint\DeleteEvent;
use App\Event\TimeStampableCreatedEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use App\Tests\IntegrationTester;
use Codeception\Test\Unit;

/**
 * @coversNothing
 * Class DeleteComplaintUserStays
 * @package App\Tests\integration\User
 */
class DeleteComplaintUserStays extends Unit
{
	use EventDispatcherConcern;
	use EntityManagerConcern;
	
	/**
	 * @var IntegrationTester
	 */
	protected IntegrationTester $tester;
	
	public function testDelete()
	{
		$ed = $this->getEd();
		$em = $this->getEm();
		
		$user = $em->find(User::class, 1);
		
		$complaint = new Complaint();
		
		$ed->dispatch(
			new AuthorableCreatedEvent($complaint, $user)
		);
		
		$ed->dispatch(
			new TimeStampableCreatedEvent($complaint)
		);
		
		$em->persist($complaint);
		$em->flush();
		
		$this->tester->canSeeInRepository(
			Complaint::class,
			[
				'author' => $complaint->getAuthor(),
			]
		);
		
		$ed->dispatch(
			new DeleteEvent($complaint, $user)
		);
		
		$em->flush();
		
		$this->tester->cantSeeInRepository(
			Complaint::class,
			[
				'author' => $complaint->getAuthor(),
			]
		);
		
		$this->tester->canSeeInRepository(
			User::class,
			[
				'id' => $user->getId(),
			]
		);
	}
}