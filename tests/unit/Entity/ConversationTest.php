<?php

declare(strict_types = 1);

namespace App\Tests\unit\Entity;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

/**
 * @covers \App\Entity\Conversation
 * Class ConversationTest
 * @package App\Tests\unit\Entity
 */
class ConversationTest extends Unit
{
	public function testDefaultProps()
	{
		$conversation = new Conversation();
		
		$ac = new ArrayCollection();
		
		$this->assertNull($conversation->getId());
		$this->assertNull($conversation->getAuthor());
		$this->assertEquals($ac, $conversation->getParticipants());
		$this->assertEquals(0, $conversation->getParticipants()->count());
		$this->assertEquals($ac, $conversation->getMessages());
		$this->assertEquals(0, $conversation->getMessages()->count());
		$this->assertNull($conversation->getCreatedAt());
		$this->assertNull($conversation->getUpdatedAt());
	}
	
	/**
	 * @throws Exception
	 */
	public function testAddAndRemoveParticipant()
	{
		/** @var User $user */
		$user = Stub::make(User::class);
		
		$conversation = new Conversation();
		
		$conversation->addParticipant($user);
		
		$this->assertTrue($conversation->getParticipants()->contains($user));
		
		$conversation->removeParticipant($user);
		
		$this->assertFalse($conversation->getParticipants()->contains($user));
	}
	
	/**
	 * @throws Exception
	 */
	public function testAddAndRemoveMessage()
	{
		/** @var Message $message */
		$message = Stub::make(Message::class);
		
		$conversation = new Conversation();
		
		$conversation->addMessage($message);
		
		$this->assertTrue($conversation->getMessages()->contains($message));
		
		$conversation->removeMessage($message);
		
		$this->assertFalse($conversation->getMessages()->contains($message));
	}
}