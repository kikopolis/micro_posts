<?php

declare(strict_types = 1);

namespace App\Tests\integration\User;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Event\AuthorableCreatedEvent;
use App\Event\Message\DeleteEvent;
use App\Event\TimeStampableCreatedEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use App\Tests\IntegrationTester;
use Codeception\Test\Unit;

/**
 * @coversNothing
 * Class DeleteMessageUserStaysTest
 * @package App\Tests\integration\User
 */
class DeleteMessageUserStaysTest extends Unit
{
	use EventDispatcherConcern;
	use EntityManagerConcern;
	
	/**
	 * @var IntegrationTester
	 */
	protected IntegrationTester $tester;
	
	public function testDeleteMessageUserStays()
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
		
		$body    = 'this is a message';
		$message = new Message($body);
		
		$message->setConversation($conversation);
		
		$ed->dispatch(
			new AuthorableCreatedEvent($message, $user)
		);
		
		$ed->dispatch(
			new TimeStampableCreatedEvent($message)
		);
		
		$em->persist($conversation);
		$em->persist($message);
		$em->flush();
		
		$this->tester->canSeeInRepository(
			Message::class,
			[
				'body' => $body,
			]
		);
		
		$ed->dispatch(
			new DeleteEvent($message, $user)
		);
		
		$em->flush();
		
		$this->tester->canSeeInRepository(
			Conversation::class,
			[
				'id' => $conversation->getId(),
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