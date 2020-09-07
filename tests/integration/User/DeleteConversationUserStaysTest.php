<?php

declare(strict_types = 1);

namespace App\Tests\integration\User;

use App\Entity\Conversation;
use App\Entity\User;
use App\Event\AuthorableCreatedEvent;
use App\Event\Conversation\DeleteEvent;
use App\Event\TimeStampableCreatedEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use App\Tests\IntegrationTester;
use Codeception\Test\Unit;

/**
 * @coversNothing
 * Class DeleteConversationUserStaysTest
 * @package App\Tests\integration\User
 */
class DeleteConversationUserStaysTest extends Unit
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
		
		$conversation = new Conversation();
		
		$ed->dispatch(
			new AuthorableCreatedEvent($conversation, $user)
		);
		
		$ed->dispatch(
			new TimeStampableCreatedEvent($conversation)
		);
		
		$em->persist($conversation);
		$em->flush();
		
		$this->tester->canSeeInRepository(
			Conversation::class,
			[
				'author' => $conversation->getAuthor(),
			]
		);
		
		$ed->dispatch(
			new DeleteEvent($conversation, $user)
		);
		
		$em->flush();
		
		$this->tester->cantSeeInRepository(
			Conversation::class,
			[
				'author' => $conversation->getAuthor(),
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