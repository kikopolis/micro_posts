<?php

declare(strict_types = 1);

namespace App\Tests\unit\Entity;

use App\Entity\Complaint;
use App\Entity\User;
use App\Service\TokenGenerator;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;
use Symfony\Component\Validator\Validation;

/**
 * @covers  \App\Entity\Complaint
 * Class ComplaintTest
 * @package App\Tests\unit\Entity
 */
class ComplaintTest extends Unit
{
	public function testDefaultProps()
	{
		$complaint = new Complaint();
		
		static::assertNull($complaint->getId());
		static::assertNull($complaint->getBody());
		static::assertNull($complaint->getAuthor());
		static::assertNull($complaint->getTarget());
		static::assertNull($complaint->getCreatedAt());
		static::assertNull($complaint->getUpdatedAt());
	}
	
	public function testShortBody()
	{
		$complaint = new Complaint();
		
		$body = 'short';
		
		$complaint->setBody($body);
		
		$validator = Validation::createValidatorBuilder()
		                       ->enableAnnotationMapping()
		                       ->getValidator()
		;
		
		$errors = $validator->validate($complaint);
		
		// assertions
		static::assertTrue($errors->count() >= 1, (string) $errors);
		static::assertEquals(
			$body,
			$complaint->getBody()
		);
	}
	
	public function testLongBody()
	{
		$complaint = new Complaint();
		
		// use token to make sure bad word filter does not ruin the assertion that the body
		// is still set and equals the raw string
		$token = new TokenGenerator();
		
		$body = $token->generate(2001);
		
		$complaint->setBody($body);
		
		$validator = Validation::createValidatorBuilder()
		                       ->enableAnnotationMapping()
		                       ->getValidator()
		;
		
		$errors = $validator->validate($complaint);
		
		// assertions
		static::assertTrue($errors->count() >= 1, (string) $errors);
		static::assertEquals(
			$body,
			$complaint->getBody()
		);
	}
	
	public function testNormalBody()
	{
		$complaint = new Complaint();
		
		$body = 'body is just right';
		
		$complaint->setBody($body);
		
		$validator = Validation::createValidatorBuilder()
		                       ->enableAnnotationMapping()
		                       ->getValidator()
		;
		
		$errors = $validator->validate($complaint);
		
		// assertions
		static::assertTrue($errors->count() === 0);
		static::assertEquals(
			$body,
			$complaint->getBody()
		);
	}
	
	/**
	 * @throws Exception
	 */
	public function testBodyWithConstructorParam()
	{
		/** @var User $target */
		$target = Stub::make(
			User::class,
			[
				'getId' => 411,
			],
			$this
		);
		
		/** @var User $author */
		$author = Stub::make(
			User::class,
			[
				'getId' => 1958,
			],
			$this
		);
		
		$body = 'this is a legal comment body';
		
		$complaint = new Complaint(
			$body,
			$target,
			$author
		);
		
		$validator = Validation::createValidatorBuilder()
		                       ->enableAnnotationMapping()
		                       ->getValidator()
		;
		
		$errors = $validator->validate($complaint);
		
		// assertions
		static::assertTrue($errors->count() === 0);
		static::assertEquals(
			$body,
			$complaint->getBody()
		);
		static::assertEquals(
			$target->getId(),
			$complaint->getTarget()->getId()
		);
		static::assertEquals(
			$author->getId(),
			$complaint->getAuthor()->getId()
		);
	}
	
	/**
	 * @throws Exception
	 */
	public function testSetTarget()
	{
		/** @var User $user */
		$user = Stub::make(
			User::class,
			[
				'getId' => 411,
			],
			$this
		);
		
		$complaint = new Complaint();
		
		$complaint->setTarget($user);
		
		static::assertEquals(
			$user->getId(),
			$complaint->getTarget()->getId()
		);
	}
}