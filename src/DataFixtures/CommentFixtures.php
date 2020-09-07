<?php

declare(strict_types = 1);

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentFixtures extends BaseFixture implements DependentFixtureInterface
{
	/**
	 * @inheritDoc
	 */
	protected function loadData(ObjectManager $manager): void
	{
		$comment = new Comment();
		$faker   = Factory::create();
		$this->createMany(
			Comment::class, 750, function (Comment $comment, $i) use ($faker) {
			$comment->setBody($faker->text(240));
			$comment->setAuthor($this->getRandomReference(User::class));
			$comment->setPost($this->getRandomReference(Post::class));
			$comment->setCreationTimestamps();
			$comment->approve();
		}
		);
		
		$manager->flush();
	}
	
	/**
	 * @inheritDoc
	 */
	public function getDependencies()
	{
		return [
			UserFixtures::class,
			PostFixtures::class,
		];
	}
}
