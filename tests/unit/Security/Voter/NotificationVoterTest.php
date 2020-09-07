<?php

declare(strict_types = 1);

namespace App\Tests\unit\Security\Voter;

use App\Entity\Comment;
use App\Entity\Notification;
use App\Entity\User;
use App\Security\Voter\Contracts\VotablesContract;
use App\Security\Voter\NotificationVoter;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;
use Generator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class NotificationVoterTest
 * @package App\Tests\unit\Security\Voter
 */
class NotificationVoterTest extends Unit
{
	/**
	 * @dataProvider _supportsProvider
	 * @param  string  $attribute
	 * @param          $subject
	 * @param  bool    $expected
	 */
	public function testSupports(string $attribute, $subject, bool $expected)
	{
		$voter = new NotificationVoter();
		
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
			'subject'   => Stub::make(Notification::class),
			'expected'  => true,
		];
		
		yield 'mark read' => [
			'attribute' => VotablesContract::MARK_READ,
			'subject'   => Stub::make(Notification::class),
			'expected'  => true,
		];
		
		yield 'delete' => [
			'attribute' => VotablesContract::DELETE,
			'subject'   => Stub::make(Notification::class),
			'expected'  => true,
		];
		
		yield 'make admin' => [
			'attribute' => VotablesContract::MAKE_ADMIN,
			'subject'   => Stub::make(Notification::class),
			'expected'  => false,
		];
		
		yield 'not note' => [
			'attribute' => VotablesContract::VIEW,
			'subject'   => Stub::make(Comment::class),
			'expected'  => false,
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
		$voter = new NotificationVoter();
		
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
		yield 'view own' => [
			'attribute' => VotablesContract::VIEW,
			'subject'   => Stub::makeEmpty(
				Notification::class,
				[
					'getOwner' => Stub\Expected::atLeastOnce(
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
		
		yield 'view not own' => [
			'attribute' => VotablesContract::VIEW,
			'subject'   => Stub::makeEmpty(
				Notification::class,
				[
					'getOwner' => Stub\Expected::atLeastOnce(
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
			'subject'   => Stub::makeEmpty(
				Notification::class,
				[
					'getOwner' => Stub\Expected::atLeastOnce(
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
			'subject'   => Stub::makeEmpty(
				Notification::class,
				[
					'getOwner' => Stub\Expected::atLeastOnce(
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
		
		yield 'mark read own' => [
			'attribute' => VotablesContract::MARK_READ,
			'subject'   => Stub::makeEmpty(
				Notification::class,
				[
					'getOwner' => Stub\Expected::atLeastOnce(
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
		
		yield 'mark read not own' => [
			'attribute' => VotablesContract::MARK_READ,
			'subject'   => Stub::makeEmpty(
				Notification::class,
				[
					'getOwner' => Stub\Expected::atLeastOnce(
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
	}
}