<?php

declare(strict_types = 1);

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\UserPreferences;
use App\Entity\UserProfile;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserFixtures
 * @package App\DataFixtures
 */
class UserFixtures extends BaseFixture
{
	/**
	 * @var UserPasswordEncoderInterface
	 */
	private UserPasswordEncoderInterface $passwordEncoder;
	
	/**
	 * @var string
	 */
	private string $defaultLocale;
	
	/**
	 * UserFixtures constructor.
	 * @param  UserPasswordEncoderInterface  $passwordEncoder
	 * @param  string                        $defaultLocale
	 */
	public function __construct(UserPasswordEncoderInterface $passwordEncoder, string $defaultLocale)
	{
		$this->passwordEncoder = $passwordEncoder;
		$this->defaultLocale   = $defaultLocale;
	}
	
	/**
	 * @inheritDoc
	 */
	protected function loadData(ObjectManager $manager): void
	{
		$faker = Factory::create();
		
		/** testMod */
		$user = new User();
		
		$user->setUsername('testMod');
		$user->setFullName('Test Mod');
		$user->setEmail('testmod@test.com');
		$user->setPassword($this->passwordEncoder->encodePassword($user, 'secret'));
		$user->setCreationTimestamps();
		$user->activate();
		
		$user->addRole(User::ROLE_MODERATOR);
		
		$preferences = new UserPreferences($this->defaultLocale);
		$profile     = new UserProfile('images/defaultUserAvatar/defaultAvatar.jpg');
		
		$manager->persist($preferences);
		$manager->persist($profile);
		
		$user->setPreferences($preferences);
		$user->setProfile($profile);
		
		$manager->persist($user);
		
		/** testAdmin */
		$user = new User();
		
		$user->setUsername('testAdmin');
		$user->setFullName('Test Admin');
		$user->setEmail('testadmin@test.com');
		$user->setPassword($this->passwordEncoder->encodePassword($user, 'secret'));
		$user->setCreationTimestamps();
		$user->activate();
		
		$user->addRole(User::ROLE_SUPER_ADMINISTRATOR);
		
		$preferences = new UserPreferences($this->defaultLocale);
		$profile     = new UserProfile('images/defaultUserAvatar/defaultAvatar.jpg');
		
		$manager->persist($preferences);
		$manager->persist($profile);
		
		$user->setPreferences($preferences);
		$user->setProfile($profile);
		
		$manager->persist($user);
		
		/** Active users */
		$this->createMany(
			User::class, 15, function (User $user, $i) use ($faker, $manager) {
			$user->setUsername(str_replace('.', '', $faker->userName));
			$user->setFullName($faker->name);
			$user->setEmail($faker->email);
			$user->setPassword($this->passwordEncoder->encodePassword($user, 'secret'));
			$user->setCreationTimestamps();
			$user->activate();
			
			$preferences = new UserPreferences($this->defaultLocale);
			$profile     = new UserProfile('images/defaultUserAvatar/defaultAvatar.jpg');
			
			$manager->persist($preferences);
			$manager->persist($profile);
			
			$user->setPreferences($preferences);
			$user->setProfile($profile);
		}
		);
		
		/** Non active users */
		$this->createMany(
			User::class, 15, function (User $user, $i) use ($faker, $manager) {
			$user->setUsername(str_replace('.', '', $faker->userName));
			$user->setFullName($faker->name);
			$user->setEmail($faker->email);
			$user->setPassword($this->passwordEncoder->encodePassword($user, 'secret'));
			$user->setCreationTimestamps();
			
			$preferences = new UserPreferences($this->defaultLocale);
			$profile     = new UserProfile('images/defaultUserAvatar/defaultAvatar.jpg');
			
			$manager->persist($preferences);
			$manager->persist($profile);
			
			$user->setPreferences($preferences);
			$user->setProfile($profile);
		}
		);
		
		$this->createMany(
			User::class, 5, function (User $user, $i) use ($faker, $manager) {
			$user->setUsername(str_replace('.', '', $faker->userName));
			$user->setFullName($faker->name);
			$user->setEmail($faker->email);
			$user->setPassword($this->passwordEncoder->encodePassword($user, 'secret'));
			$user->setCreationTimestamps();
			$user->activate();
			$user->addRole(User::ROLE_MODERATOR);
			
			$preferences = new UserPreferences($this->defaultLocale);
			$profile     = new UserProfile('images/defaultUserAvatar/defaultAvatar.jpg');
			
			$manager->persist($preferences);
			$manager->persist($profile);
			
			$user->setPreferences($preferences);
			$user->setProfile($profile);
		}
		);
		
		$this->createMany(
			User::class, 2, function (User $user, $i) use ($faker, $manager) {
			$user->setUsername(str_replace('.', '', $faker->userName));
			$user->setFullName($faker->name);
			$user->setEmail($faker->email);
			$user->setPassword($this->passwordEncoder->encodePassword($user, 'secret'));
			$user->setCreationTimestamps();
			$user->activate();
			$user->addRole(User::ROLE_ADMINISTRATOR);
			
			$preferences = new UserPreferences($this->defaultLocale);
			$profile     = new UserProfile('images/defaultUserAvatar/defaultAvatar.jpg');
			
			$manager->persist($preferences);
			$manager->persist($profile);
			
			$user->setPreferences($preferences);
			$user->setProfile($profile);
		}
		);
		
		$manager->flush();
	}
}
