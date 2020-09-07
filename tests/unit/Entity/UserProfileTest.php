<?php

declare(strict_types = 1);

namespace App\Tests\unit\Entity;

use App\Entity\UserProfile;
use App\Service\TokenGenerator;
use Codeception\Test\Unit;
use DateTime;
use Symfony\Component\Validator\Validation;

/**
 * @covers  \App\Entity\UserProfile
 * Class UserProfileTest
 * @package App\Tests\unit\Entity
 */
class UserProfileTest extends Unit
{
	public function testDefaultProps()
	{
		$profile = new UserProfile();
		
		static::assertNull($profile->getId());
		static::assertNull($profile->getUser());
		static::assertEquals(
			UserProfile::DEFAULT_AVATAR,
			$profile->getAvatar()
		);
		static::assertNull($profile->getBio());
		static::assertNull($profile->getBirthday());
	}
	
	public function testBio()
	{
		$bio = (new TokenGenerator())->letters(10001);
		
		$profile = new UserProfile();
		
		$profile->setBio($bio);
		
		$validator = Validation::createValidatorBuilder()
		                       ->enableAnnotationMapping()
		                       ->getValidator()
		;
		
		$errors = $validator->validateProperty($profile, 'bio');
		
		static::assertEquals(1, $errors->count());
	}
	
	public function testConstructorParams()
	{
		$avatar = 'avatar.jpg';
		$bio    = 'this is my bio';
		$bDay   = new DateTime('12.04.1987');
		
		$profile = new UserProfile(
			$avatar,
			$bio,
			$bDay
		);
		
		static::assertEquals($avatar, $profile->getAvatar());
		static::assertEquals($bio, $profile->getBio());
		static::assertEquals($bDay, $profile->getBirthday());
	}
	
	public function testSetAvatar()
	{
		$profile = new UserProfile();
		
		$avatar = 'avatar.jpg';
		
		$profile->setAvatar($avatar);
		
		static::assertEquals($avatar, $profile->getAvatar());
		
		$profile->setAvatar(null);
		
		static::assertEquals(
			UserProfile::DEFAULT_AVATAR,
			$profile->getAvatar()
		);
	}
}