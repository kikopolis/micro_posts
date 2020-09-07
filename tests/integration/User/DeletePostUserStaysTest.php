<?php

declare(strict_types = 1);

namespace App\Tests\integration\User;

use App\Entity\Post;
use App\Entity\User;
use App\Event\AuthorableCreatedEvent;
use App\Event\Post\CreateEvent;
use App\Event\Post\DeleteEvent;
use App\Event\TimeStampableCreatedEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use App\Tests\IntegrationTester;
use Codeception\Test\Unit;

/**
 * @coversNothing
 * Class DeletePostUserStaysTest
 * @package App\Tests\integration\User
 */
class DeletePostUserStaysTest extends Unit
{
	use EventDispatcherConcern;
	use EntityManagerConcern;
	
	/**
	 * @var IntegrationTester
	 */
	protected IntegrationTester $tester;
	
	public function testDeletePostUserStaysTest()
	{
		$ed = $this->getEd();
		$em = $this->getEm();
		
		$user = $em->find(User::class, 1);
		
		$body = 'this is a valid post body. This should be saved!';
		
		$post = new Post($body);
		
		$ed->dispatch(
			new AuthorableCreatedEvent($post, $user)
		);
		
		$ed->dispatch(
			new CreateEvent($post)
		);
		
		$ed->dispatch(
			new TimeStampableCreatedEvent($post)
		);
		
		$em->persist($post);
		$em->flush();
		
		// assertions
		$this->tester->canSeeInRepository(
			Post::class,
			[
				'body' => $body,
			]
		);
		
		static::assertEquals(
			$user->getId(),
			$post->getAuthor()->getId()
		);
		
		$ed->dispatch(
			new DeleteEvent($user, $post)
		);
		
		$em->flush();
		
		// assertions
		$this->tester->canSeeInRepository(
			User::class,
			[
				'id' => $user->getId(),
			]
		);
		
		$this->tester->cantSeeInRepository(
			Post::class,
			[
				'body' => $body,
			]
		);
	}
}