<?php

declare(strict_types = 1);

namespace App\Tests\unit\Security\Voter\Concerns;

use App\Entity\Contracts\AuthorableContract;
use App\Entity\Contracts\OwnableContract;
use App\Entity\Contracts\TimeStampableContract;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Security\Voter\Concerns\CheckPermissionConcern;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;
use Generator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * @covers  \App\Security\Voter\Concerns\CheckPermissionConcern
 * Class ChecksPermissionConcernTest
 * @package App\Tests\unit\Security\Voter\Concerns
 */
class ChecksPermissionConcernTest extends Unit
{
	/**
	 * @throws Exception
	 */
	public function testHasUser()
	{
		$user = Stub::make(User::class);
		
		/** @var TokenInterface $token */
		$token = Stub::makeEmpty(
			TokenInterface::class,
			[
				'getUser' => Stub\Expected::once($user),
			],
			$this
		);
		
		$checkPerm = $this->getMockForTrait(CheckPermissionConcern::class);
		
		static::assertTrue($checkPerm->hasUser($token));
	}
	
	/**
	 * @throws Exception
	 */
	public function testHasUserNot()
	{
		/** @var TokenInterface $token */
		$token = Stub::makeEmpty(
			TokenInterface::class,
			[
				'getUser' => Stub\Expected::once('anon.'),
			],
			$this
		);
		
		$checkPerm = $this->getMockForTrait(CheckPermissionConcern::class);
		
		static::assertFalse($checkPerm->hasUser($token));
	}
	
	/**
	 * @dataProvider _isModeratorProvider
	 * @param  bool  $hasRole
	 * @param  bool  $expected
	 * @throws Exception
	 */
	public function testIsModerator(bool $hasRole, bool $expected)
	{
		$user = Stub::make(
			User::class,
			[
				'hasRole' => $hasRole,
			],
			$this
		);
		
		/** @var TokenInterface $token */
		$token = Stub::makeEmpty(
			TokenInterface::class,
			[
				'getUser' => Stub\Expected::atLeastOnce($user),
			],
			$this
		);
		
		$checkPerm = $this->getMockForTrait(CheckPermissionConcern::class);
		
		self::assertEquals(
			$expected,
			$checkPerm->isModerator($token)
		);
	}
	
	/**
	 * @return Generator
	 */
	public function _isModeratorProvider(): Generator
	{
		yield 'moderator' => [
			'hasRole'  => true,
			'expected' => true,
		];
		
		yield 'not moderator' => [
			'hasRole'  => false,
			'expected' => false,
		];
	}
	
	/**
	 * @dataProvider _isAdminProvider
	 * @param  bool  $hasRole
	 * @param  bool  $expected
	 * @throws Exception
	 */
	public function testIsAdmin(bool $hasRole, bool $expected)
	{
		$user = Stub::make(
			User::class,
			[
				'hasRole' => $hasRole,
			],
			$this
		);
		
		/** @var TokenInterface $token */
		$token = Stub::makeEmpty(
			TokenInterface::class,
			[
				'getUser' => Stub\Expected::atLeastOnce($user),
			],
			$this
		);
		
		$checkPerm = $this->getMockForTrait(CheckPermissionConcern::class);
		
		self::assertEquals(
			$expected,
			$checkPerm->isAdmin($token)
		);
	}
	
	/**
	 * @return Generator
	 */
	public function _isAdminProvider(): Generator
	{
		yield 'admin' => [
			'hasRole'  => true,
			'expected' => true,
		];
		
		yield 'not admin' => [
			'hasRole'  => false,
			'expected' => false,
		];
	}
	
	/**
	 * @dataProvider _isCleanProvider
	 * @param  TokenInterface  $token
	 * @param  bool            $expected
	 */
	public function testIsClean(TokenInterface $token, bool $expected)
	{
		$checkPerm = $this->getMockForTrait(CheckPermissionConcern::class);
		
		static::assertEquals(
			$expected,
			$checkPerm->isClean($token)
		);
	}
	
	/**
	 * @return Generator
	 * @throws Exception
	 */
	public function _isCleanProvider(): Generator
	{
		yield 'clean' => [
			'token'    => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::exactly(
						2,
						Stub::make(
							User::class,
							[
								'isActivated' => Stub\Expected::once(true),
								'isDisabled'  => Stub\Expected::once(false),
								'isTrashed'   => Stub\Expected::once(false),
							],
							$this
						)
					),
				],
				$this
			),
			'expected' => true,
		];
		
		yield 'not activated' => [
			'token'    => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::exactly(
						2,
						Stub::make(
							User::class,
							[
								'isActivated' => Stub\Expected::once(false),
								'isDisabled'  => Stub\Expected::once(false),
								'isTrashed'   => Stub\Expected::once(false),
							],
							$this
						)
					),
				],
				$this
			),
			'expected' => false,
		];
		
		yield 'disabled' => [
			'token'    => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::exactly(
						2,
						Stub::make(
							User::class,
							[
								'isActivated' => Stub\Expected::once(true),
								'isDisabled'  => Stub\Expected::once(true),
								'isTrashed'   => Stub\Expected::once(false),
							],
							$this
						)
					),
				],
				$this
			),
			'expected' => false,
		];
		
		yield 'trashed' => [
			'token'    => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::exactly(
						2,
						Stub::make(
							User::class,
							[
								'isActivated' => Stub\Expected::once(true),
								'isDisabled'  => Stub\Expected::once(false),
								'isTrashed'   => Stub\Expected::once(true),
							],
							$this
						)
					),
				],
				$this
			),
			'expected' => false,
		];
	}
	
	/**
	 * @dataProvider _isOwnerProvider
	 * @param  TokenInterface  $token
	 * @param                  $subject
	 * @param  bool            $expected
	 */
	public function testIsOwner(
		TokenInterface $token,
		$subject,
		bool $expected
	)
	{
		$checkPerm = $this->getMockForTrait(CheckPermissionConcern::class);
		
		static::assertEquals(
			$expected,
			$checkPerm->isOwner($subject, $token)
		);
	}
	
	/**
	 * @return Generator
	 * @throws Exception
	 */
	public function _isOwnerProvider(): Generator
	{
		yield 'is author' => [
			'token'    => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::once(1111),
							],
							$this
						)
					),
				],
				$this
			),
			'subject'  => Stub::makeEmpty(
				AuthorableContract::class,
				[
					'getAuthor' => Stub\Expected::once(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::once(1111),
							],
							$this
						)
					),
				],
				$this
			),
			'expected' => true,
		];
		
		yield 'is not author' => [
			'token'    => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::once(1111),
							],
							$this
						)
					),
				],
				$this
			),
			'subject'  => Stub::makeEmpty(
				AuthorableContract::class,
				[
					'getAuthor' => Stub\Expected::once(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::once(2222),
							],
							$this
						)
					),
				],
				$this
			),
			'expected' => false,
		];
		
		yield 'is owner' => [
			'token'    => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::once(1111),
							],
							$this
						)
					),
				],
				$this
			),
			'subject'  => Stub::makeEmpty(
				OwnableContract::class,
				[
					'getOwner' => Stub\Expected::once(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::once(1111),
							],
							$this
						)
					),
				],
				$this
			),
			'expected' => true,
		];
		
		yield 'is not owner' => [
			'token'    => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::once(1111),
							],
							$this
						)
					),
				],
				$this
			),
			'subject'  => Stub::makeEmpty(
				OwnableContract::class,
				[
					'getOwner' => Stub\Expected::once(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::once(2222),
							],
							$this
						)
					),
				],
				$this
			),
			'expected' => false,
		];
		
		yield 'is self' => [
			'token'    => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::once(1111),
							],
							$this
						)
					),
				],
				$this
			),
			'subject'  => Stub::makeEmpty(
				User::class,
				[
					'getId' => Stub\Expected::once(1111),
				],
				$this
			),
			'expected' => true,
		];
		
		yield 'is not self' => [
			'token'    => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::once(1111),
							],
							$this
						)
					),
				],
				$this
			),
			'subject'  => Stub::makeEmpty(
				User::class,
				[
					'getId' => Stub\Expected::once(2222),
				],
				$this
			),
			'expected' => false,
		];
		
		yield 'method get user exists and object belongs' => [
			'token'    => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::once(1111),
							],
							$this
						)
					),
				],
				$this
			),
			'subject'  => Stub::makeEmpty(
				UserProfile::class,
				[
					'getUser' => Stub\Expected::once(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::once(1111),
							],
							$this
						)
					),
				],
				$this
			),
			'expected' => true,
		];
		
		yield 'method get user exists and object does not belong' => [
			'token'    => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::once(1111),
							],
							$this
						)
					),
				],
				$this
			),
			'subject'  => Stub::makeEmpty(
				UserProfile::class,
				[
					'getUser' => Stub\Expected::once(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::once(2222),
							],
							$this
						)
					),
				],
				$this
			),
			'expected' => false,
		];
		
		yield 'method get user does not exist and object does not belong' => [
			'token'    => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::once(1111),
							],
							$this
						)
					),
				],
				$this
			),
			'subject'  => Stub::makeEmpty(TimeStampableContract::class),
			'expected' => false,
		];
	}
}