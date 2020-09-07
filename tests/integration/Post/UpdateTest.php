<?php

declare(strict_types = 1);

namespace App\Tests\integration\Post;

use App\Entity\Notification\UserMentionedInPostNotification;
use App\Entity\Post;
use App\Entity\User;
use App\Event\Post\MentionedInPostEvent;
use App\Event\Post\UpdateEvent;
use App\Event\TimeStampableUpdatedEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use App\Tests\IntegrationTester;
use Codeception\Test\Unit;

/**
 * @coversNothing
 * Class UpdateTest
 * @package App\Tests\integration\Post
 */
class UpdateTest extends Unit
{
	use EventDispatcherConcern;
	use EntityManagerConcern;
	
	/**
	 * @var IntegrationTester
	 */
	protected IntegrationTester $tester;
	
	public function testUpdate()
	{
		$ed = $this->getEd();
		$em = $this->getEm();
		
		$post = $em->find(Post::class, 1);
		
		$author = $post->getAuthor();
		
		$oldBody = $post->getBody();
		
		$newBody = 'this is a valid new post body. this should get saved!!!!';
		
		$post->setBody($newBody);
		
		$ed->dispatch(
			new UpdateEvent($post)
		);
		
		$ed->dispatch(
			new TimeStampableUpdatedEvent($post)
		);
		
		$em->flush();
		
		// assertions
		$this->tester->canSeeInRepository(
			Post::class,
			['body' => $newBody]
		);
		
		$this->tester->cantSeeInRepository(
			Post::class,
			['body' => $oldBody]
		);
		
		static::assertTrue($post->isPublished());
		static::assertNotTrue($post->isApproved());
		
		static::assertNotNull($post->getCreatedAt());
		static::assertNotNull($post->getUpdatedAt());
		
		static::assertEquals($newBody, $post->getBody());
		static::assertNotEquals($oldBody, $post->getBody());
		
		static::assertEquals($author->getId(), $post->getAuthor()->getId());
	}
}