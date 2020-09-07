<?php

declare(strict_types = 1);

namespace App\Tests\unit\Entity;

use App\Entity\User;
use App\Entity\UserPreferences;
use App\Entity\UserProfile;
use App\Service\TokenGenerator;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Generator;
use Symfony\Component\Validator\Validation;

/**
 * @covers  \App\Entity\User
 * Class UserTest
 * @package App\Tests\unit\Entity
 */
class UserTest extends Unit
{
	public function testDefaultProps()
	{
		$user = new User();
		
		$ac = new ArrayCollection();
		
		static::assertNull($user->getId());
		static::assertNull($user->getUsername());
		static::assertNull($user->getPassword());
		static::assertNull($user->getPlainPassword());
		static::assertNull($user->getRetypedPlainPassword());
		static::assertNull($user->getEmail());
		static::assertNull($user->getOldEmail());
		static::assertNull($user->getFullName());
		static::assertEquals(
			[User::ROLE_USER],
			$user->getRoles()
		);
		static::assertNull($user->getAccountActivationToken());
		static::assertNull($user->getPasswordResetToken());
		static::assertFalse($user->isActivated());
		static::assertFalse($user->isDisabled());
		static::assertFalse($user->isForcedPasswordChange());
		static::assertNull($user->getPreferences());
		static::assertNull($user->getProfile());
		static::assertEquals($ac, $user->getFollowers());
		static::assertEquals($ac, $user->getFollowing());
		static::assertEquals($ac, $user->getPosts());
		static::assertEquals($ac, $user->getPostsLiked());
		static::assertEquals($ac, $user->getPostsReported());
		static::assertEquals($ac, $user->getPostsApproved());
		static::assertEquals($ac, $user->getPostsUnApproved());
		static::assertEquals($ac, $user->getPostsPublished());
		static::assertEquals($ac, $user->getPostsUnPublished());
		static::assertEquals($ac, $user->getPostsTrashed());
		static::assertEquals($ac, $user->getPostsRestored());
		static::assertEquals($ac, $user->getComments());
		static::assertEquals($ac, $user->getCommentsLiked());
		static::assertEquals($ac, $user->getCommentsReported());
		static::assertEquals($ac, $user->getCommentsApproved());
		static::assertEquals($ac, $user->getCommentsUnApproved());
		static::assertEquals($ac, $user->getCommentsTrashed());
		static::assertEquals($ac, $user->getCommentsRestored());
		static::assertEquals($ac, $user->getNotifications());
		static::assertEquals($ac, $user->getMessages());
		static::assertEquals($ac, $user->getConversationsCreated());
		static::assertEquals($ac, $user->getConversationsParticipated());
		static::assertEquals($ac, $user->getComplaintsSent());
		static::assertEquals($ac, $user->getComplaintsReceived());
		static::assertNull($user->getTrashedBy());
		static::assertNull($user->getTrashedAt());
		static::assertEquals($ac, $user->getUsersTrashed());
		static::assertNull($user->getCreatedAt());
		static::assertNull($user->getUpdatedAt());
	}
	
	/**
	 * @dataProvider _usernameProvider
	 * @param  string  $username
	 * @param  int     $errorCount
	 */
	public function testUsername(string $username, int $errorCount)
	{
		$user = new User();
		
		$user->setUsername($username);
		
		$validator = Validation::createValidatorBuilder()
		                       ->enableAnnotationMapping()
		                       ->getValidator()
		;
		
		$errors = $validator->validateProperty($user, 'username');
		
		static::assertEquals($errorCount, $errors->count());
		static::assertEquals($username, $user->getUsername());
	}
	
	/**
	 * @dataProvider _fullNameProvider
	 * @param  string  $fullName
	 * @param  int     $errorCount
	 */
	public function testFullName(string $fullName, int $errorCount)
	{
		$user = new User();
		
		$user->setFullName($fullName);
		
		$validator = Validation::createValidatorBuilder()
		                       ->enableAnnotationMapping()
		                       ->getValidator()
		;
		
		$errors = $validator->validateProperty($user, 'fullName');
		
		static::assertEquals($errorCount, $errors->count());
		static::assertEquals($fullName, $user->getFullName());
	}
	
	/**
	 * @dataProvider _emailProvider
	 * @param  string  $email
	 * @param  int     $errorCount
	 */
	public function testEmail(string $email, int $errorCount)
	{
		$user = new User();
		
		$user->setEmail($email);
		
		$validator = Validation::createValidatorBuilder()
		                       ->enableAnnotationMapping()
		                       ->getValidator()
		;
		
		$errors = $validator->validateProperty($user, 'email');
		
		static::assertEquals($errorCount, $errors->count());
		static::assertEquals($email, $user->getEmail());
	}
	
	/**
	 * @dataProvider _passwordProvider
	 * @param  string  $plainPassword
	 * @param  string  $retypedPlainPassword
	 * @param  int     $errorCount1
	 * @param  int     $errorCount2
	 */
	public function testPasswords(
		string $plainPassword,
		string $retypedPlainPassword,
		int $errorCount1,
		int $errorCount2
	)
	{
		$user = new User();
		
		$user->setPlainPassword($plainPassword);
		$user->setRetypedPlainPassword($retypedPlainPassword);
		
		$validator = Validation::createValidatorBuilder()
		                       ->enableAnnotationMapping()
		                       ->getValidator()
		;
		
		$errors1 = $validator->validateProperty($user, 'plainPassword');
		$errors2 = $validator->validateProperty($user, 'retypedPlainPassword');
		
		static::assertEquals($errorCount1, $errors1->count());
		static::assertEquals($errorCount2, $errors2->count());
		static::assertEquals($plainPassword, $user->getPlainPassword());
		static::assertEquals($retypedPlainPassword, $user->getRetypedPlainPassword());
	}
	
	/**
	 * @throws Exception
	 */
	public function testConstructorParams()
	{
		/** @var UserPreferences $preferences */
		$preferences = Stub::make(
			UserPreferences::class,
			[
				'getId' => 23159,
			],
			$this
		);
		
		/** @var UserProfile $profile */
		$profile = Stub::make(
			UserProfile::class,
			[
				'getId' => 29984,
			],
			$this
		);
		
		$username             = 'username_1';
		$email                = 'kiko@kiko.com';
		$fullName             = 'Kiko Kikopolis';
		$plainPassword        = 'Password12';
		$retypedPlainPassword = 'Password12';
		
		$user = new User(
			$username,
			$email,
			$fullName,
			$plainPassword,
			$retypedPlainPassword,
			$preferences,
			$profile
		);
		
		// assertions
		static::assertEquals($username, $user->getUsername());
		static::assertEquals($email, $user->getEmail());
		static::assertEquals($fullName, $user->getFullName());
		static::assertEquals(
			$plainPassword,
			$user->getPlainPassword()
		);
		static::assertEquals(
			$retypedPlainPassword,
			$user->getRetypedPlainPassword()
		);
		static::assertEquals(
			$preferences->getId(),
			$user->getPreferences()->getId()
		);
		static::assertEquals(
			$profile->getId(),
			$user->getProfile()->getId()
		);
	}
	
	public function testEraseCredentials()
	{
		$pass = 'PASsword1';
		
		$user = new User();
		
		$user->setPlainPassword($pass);
		$user->setRetypedPlainPassword($pass);
		
		$user->eraseCredentials();
		
		static::assertNull($user->getPlainPassword());
		static::assertNull($user->getRetypedPlainPassword());
	}
	
	public function testAddAndRemoveRole()
	{
		$roleNotExisting = 'ROLE_NOT_EXISTING';
		
		$user = new User();
		
		$user->addRole(User::ROLE_MODERATOR);
		$user->addRole($roleNotExisting);
		
		static::assertContains(User::ROLE_USER, $user->getRoles());
		static::assertContains(User::ROLE_MODERATOR, $user->getRoles());
		static::assertNotContains($roleNotExisting, $user->getRoles());
		
		$user->removeRole(User::ROLE_MODERATOR);
		
		static::assertNotContains(User::ROLE_MODERATOR, $user->getRoles());
	}
	
	public function testHasRole()
	{
		$role = User::ROLE_SUPER_ADMINISTRATOR;
		
		$user = new User();
		
		static::assertFalse($user->hasRole($role));
		
		$user->addRole($role);
		
		static::assertTrue($user->hasRole($role));
	}
	
	public function testFollowAndUnFollow()
	{
		$followed = new User();
		$follower = new User();
		
		$follower->follow($followed);
		
		static::assertTrue($follower->getFollowing()->contains($followed));
		
		$follower->unFollow($followed);
		
		static::assertFalse($follower->getFollowing()->contains($followed));
	}
	
	/**
	 * @return Generator
	 */
	public function _usernameProvider(): Generator
	{
		$tokenGen = new TokenGenerator();
		
		yield 'blank username' => [
			'username'   => '',
			'errorCount' => 2,
		];
		
		yield 'short username' => [
			'username'   => 'sun',
			'errorCount' => 1,
		];
		
		yield 'long username' => [
			'username'   => $tokenGen->letters(50) . '1',
			'errorCount' => 1,
		];
		
		yield 'valid username' => [
			'username'   => 'kiko_12',
			'errorCount' => 0,
		];
		
		yield 'starting with underscore' => [
			'username'   => '_kiko_12',
			'errorCount' => 1,
		];
		
		yield 'ending with underscore' => [
			'username'   => 'kiko_12_',
			'errorCount' => 1,
		];
		
		yield 'starting with number' => [
			'username'   => '9kiko_12',
			'errorCount' => 1,
		];
		
		yield 'username with space' => [
			'username'   => 'sun and kiko',
			'errorCount' => 1,
		];
		
		yield 'username with symbols' => [
			'username'   => 'sun@##!',
			'errorCount' => 1,
		];
	}
	
	/**
	 * @return Generator
	 */
	public function _fullNameProvider(): Generator
	{
		$tokenGen = new TokenGenerator();
		
		yield 'blank fullName' => [
			'fullName'   => '',
			'errorCount' => 2,
		];
		
		yield 'short fullName' => [
			'fullName'   => 'sun',
			'errorCount' => 1,
		];
		
		yield 'long fullName' => [
			'fullName'   => $tokenGen->letters(151),
			'errorCount' => 1,
		];
		
		yield 'valid fullName' => [
			'fullName'   => 'kiko kikopolis',
			'errorCount' => 0,
		];
		
		yield 'starting with space' => [
			'fullName'   => ' kiko kikopolis',
			'errorCount' => 1,
		];
		
		yield 'ending with space' => [
			'fullName'   => 'kiko kikopolis ',
			'errorCount' => 1,
		];
		
		yield 'fullName with numbers' => [
			'fullName'   => 'sun and kiko 12',
			'errorCount' => 1,
		];
		
		yield 'fullName with symbols' => [
			'fullName'   => 'sun@##!',
			'errorCount' => 1,
		];
	}
	
	/**
	 * @return Generator
	 */
	public function _emailProvider(): Generator
	{
		$tokenGen = new TokenGenerator();
		
		yield 'blank email' => [
			'email'      => '',
			'errorCount' => 2,
		];
		
		yield 'in-valid email' => [
			'email'      => 'kikopolis-kiko.com',
			'errorCount' => 1,
		];
		
		yield 'valid email' => [
			'email'      => 'kiko@kikopolis.com',
			'errorCount' => 0,
		];
		
		yield 'short email' => [
			'email'      => 'k@k.',
			'errorCount' => 2,
		];
		
		yield 'long email' => [
			'email'      => $tokenGen->letters(254) . 'kiko@kiko.com',
			'errorCount' => 1,
		];
		
		yield 'starting with space' => [
			'email'      => ' kiko kikopolis',
			'errorCount' => 1,
		];
		
		yield 'ending with space' => [
			'email'      => 'kiko kikopolis ',
			'errorCount' => 1,
		];
	}
	
	/**
	 * @return Generator
	 */
	public function _passwordProvider(): Generator
	{
		yield 'short passwords' => [
			'plainPassword'        => 'Sh1',
			'retypedPlainPassword' => 'Sh1',
			'errorCount1'          => 1,
			'errorCount2'          => 0,
		];
		
		yield 'no small letter' => [
			'plainPassword'        => 'PASSWORD1',
			'retypedPlainPassword' => 'PASSWORD1',
			'errorCount1'          => 1,
			'errorCount2'          => 0,
		];
		
		yield 'no capital letter' => [
			'plainPassword'        => 'password1',
			'retypedPlainPassword' => 'password1',
			'errorCount1'          => 1,
			'errorCount2'          => 0,
		];
		
		yield 'no number' => [
			'plainPassword'        => 'PassworD',
			'retypedPlainPassword' => 'PassworD',
			'errorCount1'          => 1,
			'errorCount2'          => 0,
		];
		
		yield 'mismatching' => [
			'plainPassword'        => 'PASSWORD1s',
			'retypedPlainPassword' => 'password1S',
			'errorCount1'          => 0,
			'errorCount2'          => 1,
		];
	}
}