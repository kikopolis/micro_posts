<?php

declare(strict_types = 1);

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\UserPreferences;
use App\Entity\UserProfile;
use App\Service\Contracts\TokenGeneratorContract;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class TestUserFixture
 * @package App\DataFixtures
 */
class TestUserFixture extends BaseFixture
{
	/**
	 * @var UserPasswordEncoderInterface
	 */
	private UserPasswordEncoderInterface $passwordEncoder;
	
	/**
	 * @var TokenGeneratorContract
	 */
	private TokenGeneratorContract $generator;
	
	/**
	 * UserFixtures constructor.
	 * @param   UserPasswordEncoderInterface   $passwordEncoder
	 * @param   TokenGeneratorContract         $generator
	 */
	public function __construct(
		UserPasswordEncoderInterface $passwordEncoder,
		TokenGeneratorContract $generator
	)
	{
		$this->passwordEncoder = $passwordEncoder;
		$this->generator       = $generator;
	}
	
	/**
	 * @param   ObjectManager   $manager
	 * @throws Exception
	 */
	protected function loadData(ObjectManager $manager): void
	{
		$testUser       = $this->testUser($manager);
		$activeTestUser = $this->activeTestUser($manager);
		
		$this->posts($manager, $testUser);
		$this->posts($manager, $activeTestUser);
	}
	
	/**
	 * @param   ObjectManager   $manager
	 * @return User
	 */
	protected function testUser(ObjectManager $manager): User
	{
		/** testUser */
		$user = new User(
			'testUser',
			'testuser@test.com',
			'Test User'
		);
		
		$user->setPassword(
			$this->passwordEncoder->encodePassword(
				$user, 'secret'
			)
		);
		
		$user->setCreationTimestamps();
		
		// Ensure user is NOT activated!
		$user->deActivate();
		
		$user->setAccountActivationToken(
			$this->generator->generate(64)
		);
		
		$preferences = new UserPreferences();
		$profile     = new UserProfile(
			'images/defaultUserAvatar/defaultAvatar.jpg',
			'This is an awesome bio for an inactive!!!!',
			new DateTime('12.04.1987')
		);
		
		$user->setPreferences($preferences);
		$user->setProfile($profile);
		
		$manager->persist($preferences);
		$manager->persist($profile);
		$manager->persist($user);
		
		$manager->flush();
		
		return $user;
	}
	
	/**
	 * @param   ObjectManager   $manager
	 * @return User
	 */
	protected function activeTestUser(ObjectManager $manager): User
	{
		/** activeTestUser */
		$activeUser = new User(
			'activeTestUser',
			'active-testuser@test.com',
			'Test User'
		);
		
		$activeUser->setPassword(
			$this->passwordEncoder->encodePassword(
				$activeUser, 'secret'
			)
		);
		
		$activeUser->setCreationTimestamps();
		
		// Ensure user is activated!
		$activeUser->activate();
		
		$preferences = new UserPreferences();
		$profile     = new UserProfile(
			'images/defaultUserAvatar/defaultAvatar.jpg',
			'This is an awesome bio for an active user!!!!',
			new DateTime('12.04.1987')
		);
		
		$activeUser->setPreferences($preferences);
		$activeUser->setProfile($profile);
		
		$manager->persist($preferences);
		$manager->persist($profile);
		$manager->persist($activeUser);
		
		$manager->flush();
		
		return $activeUser;
	}
	
	/**
	 * @param   ObjectManager   $manager
	 * @param   User            $user
	 * @throws Exception
	 */
	protected function posts(ObjectManager $manager, User $user)
	{
		$faker = Factory::create();
		
		// APPROVED POSTS
		$this->createMany(
			Post::class, 20, function (Post $post, $i) use ($user, $faker) {
			$post->setBody($faker->text(240));
			$post->setAuthor($user);
			$post->setCreationTimestamps();
			
			$post->approve();
			$post->setApprovedBy($post->getAuthor());
		}
		);
		
		// PUBLISHED POSTS
		$this->createMany(
			Post::class, 20, function (Post $post, $i) use ($user, $faker) {
			$post->setBody($faker->text(240));
			$post->setAuthor($user);
			$post->setCreationTimestamps();
			
			$post->publish();
			$post->setPublishedBy($post->getAuthor());
		}
		);
		
		// PUBLISHED AND APPROVED POSTS
		$this->createMany(
			Post::class, 20, function (Post $post, $i) use ($user, $faker) {
			$post->setBody($faker->text(240));
			$post->setAuthor($user);
			$post->setCreationTimestamps();
			
			$post->publish();
			$post->setPublishedBy($post->getAuthor());
			$post->approve();
			$post->setApprovedBy($post->getAuthor());
		}
		);
		
		// TRASHED POSTS
		$this->createMany(
			Post::class, 20, function (Post $post, $i) use ($user, $faker) {
			$post->setBody($faker->text(240));
			$post->setAuthor($user);
			$post->setCreationTimestamps();
			
			$post->trash();
			$post->setTrashedBy($post->getAuthor());
		}
		);
		
		// PUBLISHED AND APPROVED AND REPORTED POSTS
		$this->createMany(
			Post::class, 20, function (Post $post, $i) use ($user, $faker) {
			$post->setBody($faker->text(240));
			$post->setAuthor($user);
			$post->setCreationTimestamps();
			
			for ($i = 0; $i < 4; $i++) {
				
				$this->report($post);
			}
			
			$post->approve();
			$post->setApprovedBy($post->getAuthor());
			$post->publish();
			$post->setPublishedBy($post->getAuthor());
		}
		);
		
		$manager->flush();
	}
	
	/**
	 * @param   Post   $post
	 * @throws Exception
	 */
	private function report(Post $post): void
	{
		/** @var User $reporter */
		$reporter = $this->getRandomReference(User::class);
		
		if ($reporter->getUsername() === $post->getAuthor()->getUsername()) {
			
			$this->report($post);
		}
		
		$post->report($reporter);
	}
}