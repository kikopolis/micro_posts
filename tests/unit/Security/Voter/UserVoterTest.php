<?php

declare(strict_types = 1);

namespace App\Tests\unit\Security\Voter;

use App\Entity\Post;
use App\Entity\User;
use App\Security\Voter\Contracts\VotablesContract;
use App\Security\Voter\UserVoter;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;
use Generator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * @covers  \App\Security\Voter\UserVoter
 * Class UserVoterTest
 * @package App\Tests\unit\Security\Voter
 */
class UserVoterTest extends Unit
{
	/**
	 * @dataProvider _supportProvider
	 * @param  string  $attribute
	 * @param          $subject
	 * @param  bool    $expected
	 * @throws Exception
	 */
	public function testSupport(string $attribute, $subject, bool $expected)
	{
		$voter = new UserVoter();
		
		static::assertEquals(
			$expected,
			$voter->supports($attribute, $subject)
		);
	}
	
	/**
	 * @return Generator
	 * @throws Exception
	 */
	public function _supportProvider(): Generator
	{
		$user = Stub::make(User::class);
		
		yield 'view' => [
			'attribute' => VotablesContract::VIEW,
			'subject'   => $user,
			'result'    => true,
		];
		
		yield 'edit' => [
			'attribute' => VotablesContract::EDIT,
			'subject'   => $user,
			'result'    => true,
		];
		
		yield 'create' => [
			'attribute' => VotablesContract::CREATE,
			'subject'   => $user,
			'result'    => true,
		];
		
		yield 'delete' => [
			'attribute' => VotablesContract::DELETE,
			'subject'   => $user,
			'result'    => true,
		];
		
		yield 'trash' => [
			'attribute' => VotablesContract::TRASH,
			'subject'   => $user,
			'result'    => true,
		];
		
		yield 'restore' => [
			'attribute' => VotablesContract::RESTORE,
			'subject'   => $user,
			'result'    => true,
		];
		
		yield 'publish' => [
			'attribute' => VotablesContract::PUBLISH,
			'subject'   => $user,
			'result'    => false,
		];
		
		yield 'make admin' => [
			'attribute' => VotablesContract::MAKE_ADMIN,
			'subject'   => $user,
			'result'    => true,
		];
		
		yield 'not user' => [
			'attribute' => VotablesContract::CREATE,
			'subject'   => Stub::make(Post::class),
			'result'    => false,
		];
	}
	
	/**
	 * @dataProvider _voteOnAttributeProvider
	 * @param  string          $attribute
	 * @param                  $subject
	 * @param  TokenInterface  $token
	 * @param  bool            $expected
	 * @throws Exception
	 */
	public function testVoteOnAttribute(
		string $attribute,
		$subject,
		TokenInterface $token,
		bool $expected
	)
	{
		$voter = new UserVoter();
		
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
		yield 'create with no user' => [
			'attribute' => VotablesContract::CREATE,
			'subject'   => Stub::make(User::class),
			'token'     => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::atLeastOnce('anon.'),
				],
				$this
			),
			'result'    => true,
		];
		
		yield 'create with user logged in' => [
			'attribute' => VotablesContract::CREATE,
			'subject'   => Stub::make(User::class),
			'token'     => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::atLeastOnce(
						Stub::make(User::class)
					),
				],
				$this
			),
			'result'    => false,
		];
		
		yield 'view self' => [
			'attribute' => VotablesContract::VIEW,
			'subject'   => Stub::make(
				User::class,
				[
					'getId' => Stub\Expected::atLeastOnce(1111),
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
			'result'    => true,
		];
		
		yield 'view other' => [
			'attribute' => VotablesContract::VIEW,
			'subject'   => Stub::make(
				User::class,
				[
					'getId' => Stub\Expected::atLeastOnce(2222),
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
			'result'    => false,
		];
		
		yield 'edit self' => [
			'attribute' => VotablesContract::EDIT,
			'subject'   => Stub::make(
				User::class,
				[
					'getId' => Stub\Expected::atLeastOnce(1111),
				],
				$this
			),
			'token'     => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser'      => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::atLeastOnce(1111),
							],
							$this
						)
					)
				],
				$this
			),
			'result'    => true,
		];
		
		yield 'edit other' => [
			'attribute' => VotablesContract::EDIT,
			'subject'   => Stub::make(
				User::class,
				[
					'getId' => Stub\Expected::atLeastOnce(2222),
				],
				$this
			),
			'token'     => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser'      => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::atLeastOnce(1111),
							],
							$this
						)
					),
					'hasAttribute' => Stub\Expected::never(),
				],
				$this
			),
			'result'    => false,
		];
		
		yield 'trash self' => [
			'attribute' => VotablesContract::TRASH,
			'subject'   => Stub::make(
				User::class,
				[
					'getId' => Stub\Expected::atLeastOnce(1111),
				],
				$this
			),
			'token'     => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser'      => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::atLeastOnce(1111),
							],
							$this
						)
					)
				],
				$this
			),
			'result'    => true,
		];
		
		yield 'trash other' => [
			'attribute' => VotablesContract::TRASH,
			'subject'   => Stub::make(
				User::class,
				[
					'getId' => Stub\Expected::atLeastOnce(2222),
				],
				$this
			),
			'token'     => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser'      => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::atLeastOnce(1111),
							],
							$this
						)
					),
					'hasAttribute' => Stub\Expected::never(),
				],
				$this
			),
			'result'    => false,
		];
		
		yield 'restore self' => [
			'attribute' => VotablesContract::RESTORE,
			'subject'   => Stub::make(
				User::class,
				[
					'getId' => Stub\Expected::atLeastOnce(1111),
				],
				$this
			),
			'token'     => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser'      => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::atLeastOnce(1111),
							],
							$this
						)
					)
				],
				$this
			),
			'result'    => true,
		];
		
		yield 'restore other' => [
			'attribute' => VotablesContract::RESTORE,
			'subject'   => Stub::make(
				User::class,
				[
					'getId' => Stub\Expected::atLeastOnce(2222),
				],
				$this
			),
			'token'     => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser'      => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::atLeastOnce(1111),
							],
							$this
						)
					),
					'hasAttribute' => Stub\Expected::never(),
				],
				$this
			),
			'result'    => false,
		];
		
		yield 'delete self' => [
			'attribute' => VotablesContract::DELETE,
			'subject'   => Stub::make(
				User::class,
				[
					'getId' => Stub\Expected::atLeastOnce(1111),
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
			'result'    => true,
		];
		
		yield 'delete other' => [
			'attribute' => VotablesContract::DELETE,
			'subject'   => Stub::make(User::class),
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
			'result'    => false,
		];
		
		yield 'publish' => [
			'attribute' => VotablesContract::PUBLISH,
			'subject'   => Stub::make(User::class),
			'token'     => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::exactly(
						2,
						Stub::make(User::class)
					),
				],
				$this
			),
			'result'    => false,
		];
		
		yield 'administrator create' => [
			'attribute' => VotablesContract::CREATE,
			'subject'   => Stub::make(User::class),
			'token'     => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser'      => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'hasRole' => Stub\Expected::atLeastOnce(true),
							],
							$this
						)
					),
				],
				$this
			),
			'result'    => false,
		];
		
		yield 'user make admin' => [
			'attribute' => VotablesContract::MAKE_ADMIN,
			'subject'   => Stub::make(User::class),
			'token'     => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::exactly(
						2,
						Stub::make(
							User::class,
							[
								'hasRole' => Stub\Expected::atLeastOnce(false),
							],
							$this
						)
					),
				],
				$this
			),
			'result'    => false,
		];
		
		yield 'administrator make admin' => [
			'attribute' => VotablesContract::MAKE_ADMIN,
			'subject'   => Stub::make(User::class),
			'token'     => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::exactly(
						2,
						Stub::make(
							User::class,
							[
								'hasRole' => Stub\Expected::atLeastOnce(true),
							],
							$this
						)
					),
				],
				$this
			),
			'result'    => true,
		];
	}
}