<?php

declare(strict_types = 1);

namespace App\Tests\unit\Entity;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Service\TokenGenerator;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;
use Symfony\Component\Validator\Validation;

/**
 * @covers \App\Entity\Message
 * Class MessageTest
 * @package App\Tests\unit\Entity
 */
class MessageTest extends Unit
{
	public function testDefaultProps()
	{
		$message = new Message();
		
		$this->assertNull($message->getId());
		$this->assertNull($message->getAuthor());
		$this->assertNull($message->getBody());
		$this->assertNull($message->getConversation());
		$this->assertNull($message->getCreatedAt());
		$this->assertNull($message->getUpdatedAt());
	}
	
	public function testShortBody()
	{
		$message = new Message();
		
		$body = '';
		
		$message->setBody($body);
		
		$validator = Validation::createValidatorBuilder()
		                       ->enableAnnotationMapping()
		                       ->getValidator()
		;
		
		$errors = $validator->validate($message);
		
		// assertions
		$this->assertTrue($errors->count() >= 1, (string) $errors);
		$this->assertEquals(
			$body,
			$message->getBody()
		);
	}
	
	public function testLongBody()
	{
		$message = new Message();
		
		// use token generator to make a long body without curse filter cutting words
		$tokenGen = new TokenGenerator();
		
		$body = $tokenGen->generate(2501);
		
		$message->setBody($body);
		
		$validator = Validation::createValidatorBuilder()
		                       ->enableAnnotationMapping()
		                       ->getValidator()
		;
		
		$errors = $validator->validate($message);
		
		// assertions
		$this->assertTrue($errors->count() >= 1, (string) $errors);
		$this->assertEquals(
			$body,
			$message->getBody()
		);
	}
	
	public function testValidBody()
	{
		$message = new Message();
		
		$body = 'this is a valid body.';
		
		$message->setBody($body);
		
		$validator = Validation::createValidatorBuilder()
		                       ->enableAnnotationMapping()
		                       ->getValidator()
		;
		
		$errors = $validator->validate($message);
		
		// assertions
		$this->assertTrue($errors->count() === 0, (string) $errors);
		$this->assertEquals(
			$body,
			$message->getBody()
		);
	}
	
	/**
	 * @throws Exception
	 */
	public function testConstructorParams()
	{
		$body = 'this is another valid body';
		
		/** @var Conversation $conversation */
		$conversation = Stub::make(
			Conversation::class,
			[
				'getId' => 2238,
			],
			$this
		);
		
		/** @var User $author */
		$author = Stub::make(
			User::class,
			[
				'getId' => 9494,
			],
			$this
		);
		
		$message = new Message(
			$body,
			$conversation,
			$author
		);
		
		$validator = Validation::createValidatorBuilder()
		                       ->enableAnnotationMapping()
		                       ->getValidator()
		;
		
		$errors = $validator->validate($message);
		
		// assertions
		$this->assertTrue($errors->count() === 0, (string) $errors);
		$this->assertEquals(
			$body,
			$message->getBody()
		);
		$this->assertEquals(
			$conversation->getId(),
			$message->getConversation()->getId()
		);
		$this->assertEquals(
			$author->getId(),
			$message->getAuthor()->getId()
		);
	}
	
	/**
	 * @throws Exception
	 */
	public function testSetConversation()
	{
		$message = new Message();
		
		/** @var Conversation $conversation */
		$conversation = Stub::make(
			Conversation::class,
			[
				'getId' => 2238,
			],
			$this
		);
		
		$message->setConversation($conversation);
		
		// assertions
		$this->assertEquals(
			$conversation->getId(),
			$message->getConversation()->getId()
		);
	}
}