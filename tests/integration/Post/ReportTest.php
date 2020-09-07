<?php

declare(strict_types = 1);

namespace App\Tests\integration\Post;

use App\Entity\Notification\PostReportedNotification;
use App\Entity\Post;
use App\Entity\User;
use App\Event\Post\ReportEvent;
use App\Tests\integration\Concern\EntityManagerConcern;
use App\Tests\integration\Concern\EventDispatcherConcern;
use App\Tests\IntegrationTester;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\Criteria;

/**
 * @coversNothing
 * Class ReportTest
 * @package App\Tests\integration\Post
 */
class ReportTest extends Unit
{
	use EventDispatcherConcern;
	use EntityManagerConcern;
	
	/**
	 * @var IntegrationTester
	 */
	protected IntegrationTester $tester;
	
	public function testReport()
	{
		$ed = $this->getEd();
		$em = $this->getEm();
		
		$mods = $this->tester->grabEntitiesFromRepository(
			User::class,
			[
				Criteria::expr()->contains('roles', User::ROLE_MODERATOR),
			]
		);
		
		$user = $em->find(User::class, 1);
		
		$post = $em->find(Post::class, 1);
		
		$ed->dispatch(
			new ReportEvent($mods, $user, $post)
		);
		
		$em->flush();
		
		// assertions
		static::assertTrue($post->isReported());
		static::assertNotEmpty($post->getReportedBy());
		
		static::assertTrue($user->getPostsReported()->contains($post));
		
		foreach ($mods as $mod) {
			$this->tester->canSeeInRepository(
				PostReportedNotification::class,
				[
					'owner' => $mod,
				]
			);
		}
		
	}
}