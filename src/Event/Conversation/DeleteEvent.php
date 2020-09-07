<?php

declare(strict_types = 1);

namespace App\Event\Conversation;

use App\Entity\Conversation;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class DeleteEvent
 * @package App\Event\Conversation
 */
class DeleteEvent extends Event
{
	const NAME = 'conversation.deleted';
	
	/**
	 * @var Conversation
	 */
	private Conversation $conversation;
	
	/**
	 * @var User
	 */
	private User $deletedBy;
	
	/**
	 * DeleteEvent constructor.
	 * @param  Conversation  $conversation
	 * @param  User          $deletedBy
	 */
	public function __construct(Conversation $conversation, User $deletedBy)
	{
		$this->conversation = $conversation;
		$this->deletedBy    = $deletedBy;
	}
	
	/**
	 * @return Conversation
	 */
	public function getConversation(): Conversation
	{
		return $this->conversation;
	}
	
	/**
	 * @return User
	 */
	public function getDeletedBy(): User
	{
		return $this->deletedBy;
	}
}