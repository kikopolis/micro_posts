<?php

declare(strict_types = 1);

namespace App\Entity\Notification;

use App\Entity\Contracts\NotificationContract;
use App\Entity\Conversation;
use App\Entity\Notification;
use App\Entity\User;
use App\Entity\Message;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * Class MessageNotification
 * @package App\Entity\Notification
 */
class NewMessageNotification extends Notification implements NotificationContract
{
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Message")
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 * @var Message
	 */
	protected Message $message;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Conversation")
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 * @var Conversation
	 */
	protected Conversation $conversation;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User")
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 * @var User
	 */
	protected User $postedBy;
	
	/**
	 * MessageNotification constructor.
	 * @param  User          $owner
	 * @param  Message       $message
	 * @param  Conversation  $conversation
	 * @param  User          $postedBy
	 */
	public function __construct(
		User $owner,
		Message $message,
		Conversation $conversation,
		User $postedBy
	)
	{
		$this->owner        = $owner;
		$this->message      = $message;
		$this->conversation = $conversation;
		$this->postedBy     = $postedBy;
	}
	
	/**
	 * @return User
	 */
	public function getPostedBy(): User
	{
		return $this->postedBy;
	}
	
	/**
	 * @return Message
	 */
	public function getMessage(): Message
	{
		return $this->message;
	}
	
	/**
	 * @return Conversation
	 */
	public function getConversation(): Conversation
	{
		return $this->conversation;
	}
	
	/**
	 * @return string
	 */
	public function getText(): string
	{
		return sprintf(
			'User "%s" has posted a new message in a conversation of yours.',
			$this->getPostedBy()->getUsername()
		);
	}
}
