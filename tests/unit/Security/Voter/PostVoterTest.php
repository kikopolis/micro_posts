<?php

declare(strict_types = 1);

namespace App\Tests\unit\Security\Voter;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Security\Voter\Contracts\VotablesContract;
use App\Security\Voter\PostVoter;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;
use Generator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class PostVoterTest
 * @package App\Tests\unit\Security\Voter
 */
class PostVoterTest extends Unit
{
	/**
	 * @dataProvider _supportsProvider
	 * @param  string  $attribute
	 * @param          $subject
	 * @param  bool    $expected
	 */
	public function testSupports(string $attribute, $subject, bool $expected)
	{
		$voter = new PostVoter();
		
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
			'subject'   => Stub::make(Post::class),
			'expected'  => true,
		];
		
		yield 'create' => [
			'attribute' => VotablesContract::CREATE,
			'subject'   => Stub::make(Post::class),
			'expected'  => true,
		];
		
		yield 'edit' => [
			'attribute' => VotablesContract::EDIT,
			'subject'   => Stub::make(Post::class),
			'expected'  => true,
		];
		
		yield 'trash' => [
			'attribute' => VotablesContract::TRASH,
			'subject'   => Stub::make(Post::class),
			'expected'  => true,
		];
		
		yield 'restore' => [
			'attribute' => VotablesContract::RESTORE,
			'subject'   => Stub::make(Post::class),
			'expected'  => true,
		];
		
		yield 'delete' => [
			'attribute' => VotablesContract::DELETE,
			'subject'   => Stub::make(Post::class),
			'expected'  => true,
		];
		
		yield 'publish' => [
			'attribute' => VotablesContract::PUBLISH,
			'subject'   => Stub::make(Post::class),
			'expected'  => true,
		];
		
		yield 'un publish' => [
			'attribute' => VotablesContract::UN_PUBLISH,
			'subject'   => Stub::make(Post::class),
			'expected'  => true,
		];
		
		yield 'approve' => [
			'attribute' => VotablesContract::APPROVE,
			'subject'   => Stub::make(Post::class),
			'expected'  => true,
		];
		
		yield 'un approve' => [
			'attribute' => VotablesContract::UN_APPROVE,
			'subject'   => Stub::make(Post::class),
			'expected'  => true,
		];
		
		yield 'make admin' => [
			'attribute' => VotablesContract::MAKE_ADMIN,
			'subject'   => Stub::make(Post::class),
			'expected'  => false,
		];
		
		yield 'not post' => [
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
		$voter = new PostVoter();
		
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
		yield 'view' => [
			'attribute' => VotablesContract::VIEW,
			'subject'   => Stub::make(
				Post::class,
				[
					'isPublished' => true,
					'isApproved'  => true,
					'isTrashed'   => false,
				],
				$this
			),
			'token'     => Stub::makeEmpty(TokenInterface::class),
			'expected'  => true,
		];
		
		yield 'view un published' => [
			'attribute' => VotablesContract::VIEW,
			'subject'   => Stub::make(
				Post::class,
				[
					'getAuthor'   => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::atLeastOnce(1111),
							],
							$this
						)
					),
					'isPublished' => false,
					'isApproved'  => true,
					'isTrashed'   => false,
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
		
		yield 'view un approved' => [
			'attribute' => VotablesContract::VIEW,
			'subject'   => Stub::make(
				Post::class,
				[
					'getAuthor'   => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::atLeastOnce(1111),
							],
							$this
						)
					),
					'isPublished' => true,
					'isApproved'  => false,
					'isTrashed'   => false,
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
		
		yield 'view trashed' => [
			'attribute' => VotablesContract::VIEW,
			'subject'   => Stub::make(
				Post::class,
				[
					'getAuthor'   => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::atLeastOnce(1111),
							],
							$this
						)
					),
					'isPublished' => true,
					'isApproved'  => true,
					'isTrashed'   => true,
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
		
		yield 'view un published as author' => [
			'attribute' => VotablesContract::VIEW,
			'subject'   => Stub::make(
				Post::class,
				[
					'getAuthor'   => Stub\Expected::atLeastOnce(
						Stub::make(
							User::class,
							[
								'getId' => Stub\Expected::atLeastOnce(1111),
							],
							$this
						)
					),
					'isPublished' => false,
					'isApproved'  => true,
					'isTrashed'   => false,
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
		
		yield 'create' => [
			'attribute' => VotablesContract::CREATE,
			'subject'   => Stub::make(Post::class),
			'token'     => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::atLeastOnce(
						Stub::make(User::class)
					),
				],
				$this
			),
			'expected'  => true,
		];
		
		yield 'create with no user' => [
			'attribute' => VotablesContract::CREATE,
			'subject'   => Stub::make(Post::class),
			'token'     => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::atLeastOnce('anon'),
				],
				$this
			),
			'expected'  => false,
		];
		
		yield 'edit own' => [
			'attribute' => VotablesContract::EDIT,
			'subject'   => Stub::make(
				Post::class,
				[
					'getAuthor' => Stub\Expected::atLeastOnce(
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
				Post::class,
				[
					'getAuthor' => Stub\Expected::atLeastOnce(
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
		
		yield 'trash own' => [
			'attribute' => VotablesContract::TRASH,
			'subject'   => Stub::make(
				Post::class,
				[
					'getAuthor' => Stub\Expected::atLeastOnce(
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
		
		yield 'trash not own' => [
			'attribute' => VotablesContract::TRASH,
			'subject'   => Stub::make(
				Post::class,
				[
					'getAuthor' => Stub\Expected::atLeastOnce(
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
		
		yield 'restore own' => [
			'attribute' => VotablesContract::RESTORE,
			'subject'   => Stub::make(
				Post::class,
				[
					'getAuthor' => Stub\Expected::atLeastOnce(
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
		
		yield 'restore not own' => [
			'attribute' => VotablesContract::RESTORE,
			'subject'   => Stub::make(
				Post::class,
				[
					'getAuthor' => Stub\Expected::atLeastOnce(
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
				Post::class,
				[
					'getAuthor' => Stub\Expected::atLeastOnce(
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
				Post::class,
				[
					'getAuthor' => Stub\Expected::atLeastOnce(
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
		
		yield 'publish own' => [
			'attribute' => VotablesContract::PUBLISH,
			'subject'   => Stub::make(
				Post::class,
				[
					'getAuthor' => Stub\Expected::atLeastOnce(
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
		
		yield 'publish not own' => [
			'attribute' => VotablesContract::PUBLISH,
			'subject'   => Stub::make(
				Post::class,
				[
					'getAuthor' => Stub\Expected::atLeastOnce(
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
		
		yield 'un publish own' => [
			'attribute' => VotablesContract::UN_PUBLISH,
			'subject'   => Stub::make(
				Post::class,
				[
					'getAuthor' => Stub\Expected::atLeastOnce(
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
		
		yield 'un publish not own' => [
			'attribute' => VotablesContract::UN_PUBLISH,
			'subject'   => Stub::make(
				Post::class,
				[
					'getAuthor' => Stub\Expected::atLeastOnce(
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
		
		yield 'approve own not mod' => [
			'attribute' => VotablesContract::APPROVE,
			'subject'   => Stub::make(
				Post::class,
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
								'getId' => Stub\Expected::never(),
							],
							$this
						)
					),
				],
				$this
			),
			'expected'  => false,
		];
		
		yield 'un approve own not mod' => [
			'attribute' => VotablesContract::UN_APPROVE,
			'subject'   => Stub::make(
				Post::class,
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
								'getId' => Stub\Expected::never(),
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
				Post::class,
				[
					'getAuthor'   => Stub\Expected::never(),
					'isPublished' => Stub\Expected::never(),
					'isApproved'  => Stub\Expected::never(),
					'isTrashed'   => Stub\Expected::never(),
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