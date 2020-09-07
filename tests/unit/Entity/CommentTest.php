<?php

declare(strict_types = 1);

namespace App\Tests\unit\Entity;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Service\TokenGenerator;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Symfony\Component\Validator\Validation;

/**
 * @covers \App\Entity\Comment
 * Class CommentTest
 * @package App\Tests\unit\Entity
 */
class CommentTest extends Unit
{
	public function testDefaultProps()
	{
		$comment = new Comment();
		
		$ac = new ArrayCollection();
		
		static::assertNull($comment->getId());
		static::assertNull($comment->getBody());
		static::assertNull($comment->getAuthor());
		static::assertNull($comment->getPost());
		static::assertFalse($comment->isReported());
		static::assertEquals(0, $comment->getReportCount());
		static::assertEquals($ac, $comment->getReportedBy());
		static::assertEquals(0, $comment->getViewCount());
		static::assertEquals(0, $comment->getWeeklyViewCount());
		static::assertEquals(0, $comment->getMonthlyViewCount());
		static::assertEquals($ac, $comment->getLikedBy());
		static::assertEquals(0, $comment->getLikeCount());
		static::assertEquals(0, $comment->getWeeklyLikeCount());
		static::assertNull($comment->getApprovedBy());
		static::assertNull($comment->getUnApprovedBy());
		static::assertNull($comment->getApprovedAt());
		static::assertNull($comment->getTrashedBy());
		static::assertNull($comment->getTrashedAt());
		static::assertNull($comment->getRestoredBy());
		static::assertNull($comment->getRestoredAt());
		static::assertNull($comment->getCreatedAt());
		static::assertNull($comment->getUpdatedAt());
	}
	
	public function testShortBody()
	{
		$comment = new Comment();
		
		$body = 'short';
		
		$comment->setBody($body);
		
		$validator = Validation::createValidatorBuilder()
		                       ->enableAnnotationMapping()
		                       ->getValidator()
		;
		
		$errors = $validator->validate($comment);
		
		// assertions
		static::assertTrue($errors->count() >= 1, (string) $errors);
		static::assertEquals(
			$body,
			$comment->getBody()
		);
	}
	
	public function testLongBody()
	{
		$comment = new Comment();
		
		// use token to make sure bad word filter does not ruin the assertion that the body
		// is still set and equals the raw string
		$token = new TokenGenerator();
		
		$body = $token->generate(281);
		
		$comment->setBody($body);
		
		$validator = Validation::createValidatorBuilder()
		                       ->enableAnnotationMapping()
		                       ->getValidator()
		;
		
		$errors = $validator->validate($comment);
		
		// assertions
		static::assertTrue($errors->count() >= 1, (string) $errors);
		static::assertEquals(
			$body,
			$comment->getBody()
		);
	}
	
	public function testNormalBody()
	{
		$comment = new Comment();
		
		$body = 'body is just right';
		
		$comment->setBody($body);
		
		$validator = Validation::createValidatorBuilder()
		                       ->enableAnnotationMapping()
		                       ->getValidator()
		;
		
		$errors = $validator->validate($comment);
		
		// assertions
		static::assertTrue($errors->count() === 0);
		static::assertEquals(
			$body,
			$comment->getBody()
		);
	}
	
	/**
	 * @throws Exception
	 */
	public function testConstructorParams()
	{
		$body = 'this is a legal comment body';
		
		/** @var Post $post */
		$post = Stub::make(
			Post::class,
			[
				'getId' => 999,
			],
			$this
		);
		
		/** @var User $author */
		$author = Stub::make(
			User::class,
			[
				'getId' => 22341,
			],
			$this
		);
		
		$comment = new Comment(
			$body,
			$post,
			$author
		);
		
		$validator = Validation::createValidatorBuilder()
		                       ->enableAnnotationMapping()
		                       ->getValidator()
		;
		
		$errors = $validator->validate($comment);
		
		// assertions
		static::assertTrue($errors->count() === 0);
		static::assertEquals(
			$body,
			$comment->getBody()
		);
		static::assertEquals(
			$post->getId(),
			$comment->getPost()->getId()
		);
		static::assertEquals(
			$author->getId(),
			$comment->getAuthor()->getId()
		);
	}
	
	/**
	 * @throws Exception
	 */
	public function testSetPost()
	{
		/** @var Post $post */
		$post = Stub::make(
			Post::class,
			[
				'getId' => 999,
			],
			$this
		);
		
		$comment = new Comment();
		
		$comment->setPost($post);
		
		static::assertEquals(
			$post->getId(),
			$comment->getPost()->getId()
		);
	}
}