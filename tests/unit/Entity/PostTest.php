<?php

declare(strict_types = 1);

namespace App\Tests\unit\Entity;

use App\Entity\Post;
use App\Entity\User;
use App\Service\TokenGenerator;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Symfony\Component\Validator\Validation;

/**
 * @covers \App\Entity\Post
 * Class PostTest
 * @package App\Tests\unit\Entity
 */
class PostTest extends Unit
{
	public function testDefaultProps()
	{
		$post = new Post();
		
		$ac = new ArrayCollection();
		
		$this->assertNull($post->getId());
		$this->assertNull($post->getBody());
		$this->assertNull($post->getAuthor());
		$this->assertEquals($ac, $post->getComments());
		$this->assertEquals($ac, $post->getLikedBy());
		$this->assertEquals(0, $post->getLikeCount());
		$this->assertEquals(0, $post->getWeeklyViewCount());
		$this->assertFalse($post->isReported());
		$this->assertEquals(0, $post->getReportCount());
		$this->assertEquals($ac, $post->getReportedBy());
		$this->assertEquals(0, $post->getViewCount());
		$this->assertEquals(0, $post->getWeeklyViewCount());
		$this->assertEquals(0, $post->getMonthlyViewCount());
		$this->assertNull($post->getApprovedBy());
		$this->assertNull($post->getUnApprovedBy());
		$this->assertNull($post->getApprovedAt());
		$this->assertNull($post->getPublishedBy());
		$this->assertNull($post->getUnPublishedBy());
		$this->assertNull($post->getPublishedAt());
		$this->assertNull($post->getTrashedBy());
		$this->assertNull($post->getTrashedAt());
		$this->assertNull($post->getRestoredBy());
		$this->assertNull($post->getRestoredAt());
		$this->assertNull($post->getCreatedAt());
		$this->assertNull($post->getUpdatedAt());
	}
	
	public function testShortBody()
	{
		$post = new Post();
		
		$body = 'short';
		
		$post->setBody($body);
		
		$validator = Validation::createValidatorBuilder()
		                       ->enableAnnotationMapping()
		                       ->getValidator()
		;
		
		$errors = $validator->validate($post);
		
		// assertions
		$this->assertTrue($errors->count() >= 1, (string) $errors);
		$this->assertEquals(
			$body,
			$post->getBody()
		);
	}
	
	public function testLongBody()
	{
		$post = new Post();
		
		// use token to make sure bad word filter does not ruin the assertion that the body
		// is still set and equals the raw string
		$token = new TokenGenerator();
		
		$body = $token->generate(281);
		
		$post->setBody($body);
		
		$validator = Validation::createValidatorBuilder()
		                       ->enableAnnotationMapping()
		                       ->getValidator()
		;
		
		$errors = $validator->validate($post);
		
		// assertions
		$this->assertTrue($errors->count() >= 1, (string) $errors);
		$this->assertEquals(
			$body,
			$post->getBody()
		);
	}
	
	public function testNormalBody()
	{
		$post = new Post();
		
		$body = 'body is just right';
		
		$post->setBody($body);
		
		$validator = Validation::createValidatorBuilder()
		                       ->enableAnnotationMapping()
		                       ->getValidator()
		;
		
		$errors = $validator->validate($post);
		
		// assertions
		$this->assertTrue($errors->count() === 0);
		$this->assertEquals(
			$body,
			$post->getBody()
		);
	}
	
	/**
	 * @throws Exception
	 */
	public function testConstructorParams()
	{
		$body = 'this is a legal comment body';
		
		/** @var User $author */
		$author = Stub::make(
			User::class,
			[
				'getId' => 22341,
			],
			$this
		);
		
		$post = new Post(
			$body,
			$author
		);
		
		$validator = Validation::createValidatorBuilder()
		                       ->enableAnnotationMapping()
		                       ->getValidator()
		;
		
		$errors = $validator->validate($post);
		
		// assertions
		$this->assertTrue($errors->count() === 0);
		$this->assertEquals(
			$body,
			$post->getBody()
		);
		$this->assertEquals(
			$author->getId(),
			$post->getAuthor()->getId()
		);
	}
}