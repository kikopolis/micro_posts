<?php

declare(strict_types = 1);

namespace App\Event\Message;

use App\Entity\Message;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class DeleteEvent
 * @package App\Event\Message
 */
class DeleteEvent extends Event
{
	const NAME = 'message.deleted';
	
	/**
	 * @var Message
	 */
	private Message $message;
	
	/**
	 * @var User
	 */
	private User $deletedBy;
	
	/**
	 * DeleteEvent constructor.
	 * @param  Message  $message
	 * @param  User     $deletedBy
	 */
	public function __construct(Message $message, User $deletedBy)
	{
		$this->message   = $message;
		$this->deletedBy = $deletedBy;
	}
	
	/**
	 * @return Message
	 */
	public function getMessage(): Message
	{
		return $this->message;
	}
	
	/**
	 * @return User
	 */
	public function getDeletedBy(): User
	{
		return $this->deletedBy;
	}
}