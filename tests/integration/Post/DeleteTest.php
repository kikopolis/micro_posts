<?php

declare(strict_types = 1);

namespace App\Tests\integration\Post;

use App\Entity\Post;
use App\Entity\User;
use App\Event\Post\DeleteEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use App\Tests\IntegrationTester;
use Codeception\Test\Unit;

/**
 * @coversNothing
 * Class DeleteTest
 * @package App\Tests\integration\Post
 */
class DeleteTest extends Unit
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
		
		$post = $em->find(Post::class, 1);
		
		$deleter = $em->find(User::class, 1);
		
		$ed->dispatch(
			new DeleteEvent($deleter, $post)
		);
		
		$em->flush();
		
		// assertions
		self::assertNull(
			$em->find(Post::class, 1)
		);
	}
}