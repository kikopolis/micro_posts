<?php

declare(strict_types = 1);

namespace App\Event\Message;

use App\Entity\Conversation;
use App\Entity\Message;
use Symfony\Contracts\EventDispatcher\Event;

class CreateEvent extends Event
{
	const NAME = 'message.create';
	
	/**
	 * @var Message
	 */
	private Message $message;
	
	/**
	 * @var null|Conversation
	 */
	private ?Conversation $conversation;
	
	/**
	 * @var array
	 */
	private array $participants;
	
	/**
	 * MessageCreateEvent constructor.
	 * @param  Message  $message
	 */
	public function __construct(Message $message)
	{
		$this->message      = $message;
		$this->conversation = $message->getConversation();
		$this->participants = $this->conversation->getParticipants()->toArray();
	}
	
	/**
	 * @return Message
	 */
	public function getMessage(): Message
	{
		return $this->message;
	}
	
	/**
	 * @return null|Conversation
	 */
	public function getConversation(): ?Conversation
	{
		return $this->conversation;
	}
	
	/**
	 * @return array
	 */
	public function getParticipants(): array
	{
		return $this->participants;
	}
}