<?php

declare(strict_types = 1);

namespace App\Tests\unit\Security\Voter;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Security\Voter\Contracts\VotablesContract;
use App\Security\Voter\ConversationVoter;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Generator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class ConversationVoterTest
 * @package App\Tests\unit\Security\Voter
 */
class ConversationVoterTest extends Unit
{
	/**
	 * @dataProvider _supportsProvider
	 * @param  string  $attribute
	 * @param          $subject
	 * @param  bool    $expected
	 */
	public function testSupports(string $attribute, $subject, bool $expected)
	{
		$voter = new ConversationVoter();
		
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
			'subject'   => Stub::make(Conversation::class),
			'expected'  => true,
		];
		
		yield 'create' => [
			'attribute' => VotablesContract::CREATE,
			'subject'   => Stub::make(Conversation::class),
			'expected'  => true,
		];
		
		yield 'delete' => [
			'attribute' => VotablesContract::DELETE,
			'subject'   => Stub::make(Conversation::class),
			'expected'  => true,
		];
		
		yield 'add participant' => [
			'attribute' => VotablesContract::ADD_PARTICIPANT,
			'subject'   => Stub::make(Conversation::class),
			'expected'  => true,
		];
		
		yield 'make admin' => [
			'attribute' => VotablesContract::MAKE_ADMIN,
			'subject'   => Stub::make(Conversation::class),
			'expected'  => false,
		];
		
		yield 'not message' => [
			'attribute' => VotablesContract::VIEW,
			'subject'   => Stub::make(Message::class),
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
		$voter = new ConversationVoter();
		
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
			'subject'   => Stub::makeEmpty(Conversation::class),
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
		
		yield 'create with no user' => [
			'attribute' => VotablesContract::CREATE,
			'subject'   => Stub::makeEmpty(Conversation::class),
			'token'     => Stub::makeEmpty(
				TokenInterface::class,
				[
					'getUser' => Stub\Expected::atLeastOnce('anon.'),
				],
				$this
			),
			'expected'  => false,
		];
		
		yield 'view as part of conversation' => [
			'attribute' => VotablesContract::VIEW,
			'subject'   => Stub::makeEmpty(
				Conversation::class,
				[
					'getParticipants' => Stub\Expected::once(
						Stub::makeEmpty(
							ArrayCollection::class,
							[
								'contains' => Stub\Expected::once(true),
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
			'expected'  => true,
		];
		
		yield 'view as not part of conversation' => [
			'attribute' => VotablesContract::VIEW,
			'subject'   => Stub::makeEmpty(
				Conversation::class,
				[
					'getParticipants' => Stub\Expected::once(
						Stub::makeEmpty(
							ArrayCollection::class,
							[
								'contains' => Stub\Expected::once(false),
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
		
		yield 'add participant to owned conversation' => [
			'attribute' => VotablesContract::ADD_PARTICIPANT,
			'subject'   => Stub::makeEmpty(
				Message::class,
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
		
		yield 'add participant to a conversation not owned' => [
			'attribute' => VotablesContract::ADD_PARTICIPANT,
			'subject'   => Stub::makeEmpty(
				Message::class,
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
		
		yield 'delete own conversation' => [
			'attribute' => VotablesContract::DELETE,
			'subject'   => Stub::makeEmpty(
				Message::class,
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
		
		yield 'delete not own conversation' => [
			'attribute' => VotablesContract::DELETE,
			'subject'   => Stub::makeEmpty(
				Message::class,
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
	}
}