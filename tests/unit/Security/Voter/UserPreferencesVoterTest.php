<?php

declare(strict_types = 1);

namespace App\Tests\unit\Security\Voter;

use App\Entity\User;
use App\Entity\UserPreferences;
use App\Security\Voter\Contracts\VotablesContract;
use App\Security\Voter\UserPreferencesVoter;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;
use Generator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class UserPreferencesVoterTest
 * @package App\Tests\unit\Security\Voter
 */
class UserPreferencesVoterTest extends Unit
{
	/**
	 * @dataProvider _supportsProvider
	 * @param  string  $attribute
	 * @param          $subject
	 * @param  bool    $expected
	 */
	public function testSupports(string $attribute, $subject, bool $expected)
	{
		$voter = new UserPreferencesVoter();
		
		static::assertEquals(
			$expected,
			$voter->supports($attribute, $subject)
		);
	}
	
	/**
	 * @return Generator
	 * @throws Exception
	 */
	public function _supportsProvider(): Generator
	{
		yield 'view' => [
			'attribute' => VotablesContract::VIEW,
			'subject'   => Stub::make(UserPreferences::class),
			'expected'  => true,
		];
		
		yield 'create' => [
			'attribute' => VotablesContract::CREATE,
			'subject'   => Stub::make(UserPreferences::class),
			'expected'  => true,
		];
		
		yield 'edit' => [
			'attribute' => VotablesContract::EDIT,
			'subject'   => Stub::make(UserPreferences::class),
			'expected'  => true,
		];
		
		yield 'delete' => [
			'attribute' => VotablesContract::DELETE,
			'subject'   => Stub::make(UserPreferences::class),
			'expected'  => true,
		];
	}
	
	/**
	 * @dataProvider _voteOnAttributeProvider
	 * @param  string          $attribute
	 * @param                  $subject
	 * @param  TokenInterface  $token
	 * @param  bool            $expected
	 */
	public function testVoteOnAttribute(
		string $attribute,
		$subject,
		TokenInterface $token,
		bool $expected
	)
	{
		$voter = new UserPreferencesVoter();
		
		static::assertEquals(
			$expected,
			$voter->voteOnAttribute(
				$attribute,
				$subject,
				$token
			)
		);
	}
	
	/**
	 * @return Generator
	 * @throws Exception
	 */
	public function _voteOnAttributeProvider(): Generator
	{
		yield 'create with user' => [
			'attribute' => VotablesContract::CREATE,
			'subject'   => Stub::make(UserPreferences::class),
			'token'     => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::atLeastOnce(
						Stub::make(User::class)
					),
				],
				$this
			),
			'expected'  => false,
		];
		
		yield 'create with no user' => [
			'attribute' => VotablesContract::CREATE,
			'subject'   => Stub::make(UserPreferences::class),
			'token'     => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::atLeastOnce('anon.'),
				],
				$this
			),
			'expected'  => true,
		];
		
		yield 'view with anonymous user' => [
			'attribute' => VotablesContract::VIEW,
			'subject'   => Stub::make(UserPreferences::class),
			'token'     => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::atLeastOnce('anon.'),
				],
				$this
			),
			'expected'  => false,
		];
		
		yield 'view with any user' => [
			'attribute' => VotablesContract::VIEW,
			'subject'   => Stub::make(UserPreferences::class),
			'token'     => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::atLeastOnce(2222),
							],
							$this
						)
					),
				],
				$this
			),
			'expected'  => true,
		];
		
		yield 'edit own' => [
			'attribute' => VotablesContract::EDIT,
			'subject'   => Stub::make(
				UserPreferences::class,
				[
					'getUser' => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::atLeastOnce(1111),
							],
							$this
						)
					),
				],
				$this
			),
			'token'     => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::atLeastOnce(1111),
							],
							$this
						)
					),
				],
				$this
			),
			'expected'  => true,
		];
		
		yield 'edit not own' => [
			'attribute' => VotablesContract::EDIT,
			'subject'   => Stub::make(
				UserPreferences::class,
				[
					'getUser' => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::atLeastOnce(1111),
							],
							$this
						)
					),
				],
				$this
			),
			'token'     => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::atLeastOnce(2222),
							],
							$this
						)
					),
				],
				$this
			),
			'expected'  => false,
		];
		
		yield 'delete own' => [
			'attribute' => VotablesContract::DELETE,
			'subject'   => Stub::make(
				UserPreferences::class,
				[
					'getUser' => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::atLeastOnce(1111),
							],
							$this
						)
					),
				],
				$this
			),
			'token'     => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::atLeastOnce(1111),
							],
							$this
						)
					),
				],
				$this
			),
			'expected'  => true,
		];
		
		yield 'delete not own' => [
			'attribute' => VotablesContract::DELETE,
			'subject'   => Stub::make(
				UserPreferences::class,
				[
					'getUser' => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::atLeastOnce(1111),
							],
							$this
						)
					),
				],
				$this
			),
			'token'     => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::atLeastOnce(2222),
							],
							$this
						)
					),
				],
				$this
			),
			'expected'  => false,
		];
		
		yield 'admin or mod view' => [
			'attribute' => VotablesContract::VIEW,
			'subject'   => Stub::make(UserPreferences::class),
			'token'     => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'hasRole' => Stub\Expected::once(true),
								'getId'   => Stub\Expected::never(),
							],
							$this
						)
					),
				],
				$this
			),
			'expected'  => true,
		];
	}
}