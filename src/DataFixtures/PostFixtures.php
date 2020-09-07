<?php
declare(strict_types=1);
namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker\Factory;

/**
 * @codeCoverageIgnore
 * Class PostFixtures
 * @package App\DataFixtures
 */
class PostFixtures extends BaseFixture implements DependentFixtureInterface
{
	/**
	 * @param  ObjectManager  $manager
	 * @throws Exception
	 */
	protected function loadData(ObjectManager $manager): void
	{
		$faker = Factory::create();
		
		$this->createMany(
			Post::class, 250, function (Post $post, $i) use ($faker) {
			$post->setBody($faker->text(240));
			$post->setAuthor($this->getRandomReference(User::class));
			$post->setCreationTimestamps();
			$post->approve();
			$post->setApprovedBy($post->getAuthor());
			$post->publish();
			$post->setPublishedBy($post->getAuthor());
		}
		);
		
		$manager->flush();
	}
	
	/**
	 * @return string[]
	 */
	public function getDependencies(): array
	{
		return [
			UserFixtures::class,
		];
	}
}
