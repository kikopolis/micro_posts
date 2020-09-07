<?php

declare(strict_types = 1);

namespace App\Tests\unit\Security\Voter;

use App\Entity\Comment;
use App\Entity\Complaint;
use App\Entity\User;
use App\Security\Voter\ComplaintVoter;
use App\Security\Voter\Contracts\VotablesContract;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;
use Generator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class ComplaintVoterTest
 * @package App\Tests\unit\Security\Voter
 */
class ComplaintVoterTest extends Unit
{
	/**
	 * @dataProvider _supportsProvider
	 * @param  string  $attribute
	 * @param          $subject
	 * @param  bool    $expected
	 */
	public function testSupports(string $attribute, $subject, bool $expected)
	{
		$voter = new ComplaintVoter();
		
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
			'attribute' => VotablesContract::CREATE,
			'subject'   => Stub::make(Complaint::class),
			'expected'  => true,
		];
		
		yield 'delete' => [
			'attribute' => VotablesContract::DELETE,
			'subject'   => Stub::make(Complaint::class),
			'expected'  => true,
		];
		
		yield 'make admin' => [
			'attribute' => VotablesContract::MAKE_ADMIN,
			'subject'   => Stub::make(Complaint::class),
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
		$voter = new ComplaintVoter();
		
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
				Complaint::class,
				[
					'getAuthor' => Stub\Expected::never(),
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
								'hasRole' => Stub\Expected::atLeastOnce(false),
								'getId'   => Stub\Expected::never(),
							],
							$this
						)
					),
				],
				$this
			),
			'expected'  => false,
		];
		
		yield 'view not own' => [
			'attribute' => VotablesContract::VIEW,
			'subject'   => Stub::makeEmpty(
				Complaint::class,
				[
					'getAuthor' => Stub\Expected::never(),
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
								'hasRole' => Stub\Expected::atLeastOnce(false),
								'getId'   => Stub\Expected::never(),
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
				Complaint::class,
				[
					'getAuthor' => Stub\Expected::never(),
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
								'hasRole' => Stub\Expected::atLeastOnce(false),
								'getId'   => Stub\Expected::never(),
							],
							$this
						)
					),
				],
				$this
			),
			'expected'  => false,
		];
		
		yield 'delete not own' => [
			'attribute' => VotablesContract::DELETE,
			'subject'   => Stub::makeEmpty(
				Complaint::class,
				[
					'getAuthor' => Stub\Expected::never(),
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
								'hasRole' => Stub\Expected::atLeastOnce(false),
								'getId'   => Stub\Expected::never(),
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
			'subject'   => Stub::make(
				Complaint::class,
				[
					'getAuthor' => Stub\Expected::never(),
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
								'hasRole' => Stub\Expected::atLeastOnce(true),
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
		
		yield 'admin or mod delete' => [
			'attribute' => VotablesContract::DELETE,
			'subject'   => Stub::make(
				Complaint::class,
				[
					'getAuthor' => Stub\Expected::never(),
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
								'hasRole' => Stub\Expected::atLeastOnce(true),
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