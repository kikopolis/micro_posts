<?php

declare(strict_types = 1);

namespace App\Tests\integration\Post;

use App\Entity\Post;
use App\Event\Post\PublishEvent;
use App\Event\Post\UnPublishEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use App\Tests\IntegrationTester;
use Codeception\Test\Unit;

/**
 * @coversNothing
 * Class UnPublishTest
 * @package App\Tests\integration\Post
 */
class UnPublishTest extends Unit
{
	use EventDispatcherConcern;
	use EntityManagerConcern;
	
	/**
	 * @var IntegrationTester
	 */
	protected IntegrationTester $tester;
	
	public function testUnPublish()
	{
		$ed = $this->getEd();
		$em = $this->getEm();
		
		$post = $em->find(Post::class, 1);
		
		if (! $post->isPublished()) {
			
			$ed->dispatch(
				new PublishEvent($post->getAuthor(), $post)
			);
			
			$em->flush();
		}
		
		$ed->dispatch(
			new UnPublishEvent($post->getAuthor(), $post)
		);
		
		$this->tester->flushToDatabase();
		
		// assertions
		static::assertFalse($post->isPublished());
		
		static::assertNull($post->getPublishedBy());
		static::assertNull($post->getPublishedAt());
	}
}