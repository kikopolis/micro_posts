<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Entity\Concerns\OwnableConcern;
use App\Entity\Concerns\TimeStampableConcern;
use App\Entity\Contracts\OwnableContract;
use App\Entity\Contracts\TimeStampableContract;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *     "comment_like" = "App\Entity\Notification\CommentLikeNotification",
 *     "comment_posted" = "App\Entity\Notification\CommentPostedNotification",
 *     "comment_reported" = "App\Entity\Notification\CommentReportedNotification",
 *     "complaint_created" = "App\Entity\Notification\ComplaintCreatedNotification",
 *     "followed_user_creates_a_post" = "App\Entity\Notification\FollowedUserCommentsNotification",
 *     "followed_user_creates_a_comment" = "App\Entity\Notification\FollowedUserPostsNotification",
 *     "follow" = "App\Entity\Notification\FollowNotification",
 *     "new_message" = "App\Entity\Notification\NewMessageNotification",
 *     "post_like" = "App\Entity\Notification\PostLikeNotification",
 *     "post_reported" = "App\Entity\Notification\PostReportedNotification",
 *     "user_mention_comment" = "App\Entity\Notification\UserMentionedInCommentNotification",
 *     "user_mention_post" = "App\Entity\Notification\UserMentionedInPostNotification"
 * })
 * @ORM\MappedSuperclass()
 */
abstract class Notification implements OwnableContract, TimeStampableContract
{
	use OwnableConcern;
	use TimeStampableConcern;
	
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="bigint")
	 * @var null|int
	 */
	protected ?int $id = null;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="notifications")
	 * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
	 * @var ?User
	 */
	protected ?User $owner = null;
	
	/**
	 * @ORM\Column(type="boolean", nullable=false)
	 * @var bool
	 */
	protected bool $seen = false;
	
	/**
	 * @ORM\Column(type="boolean", nullable=false)
	 * @var bool
	 */
	protected bool $isModNote = false;
	
	/**
	 * @ORM\Column(type="datetime", nullable=false)
	 * @var null|DateTimeInterface
	 */
	protected ?DateTimeInterface $createdAt = null;
	
	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 * @var null|DateTimeInterface
	 */
	protected ?DateTimeInterface $updatedAt = null;
	
	/**
	 * @return null|int
	 */
	public function getId(): ?int
	{
		return $this->id;
	}
	
	/**
	 * @return bool
	 */
	public function isSeen(): ?bool
	{
		return $this->seen;
	}
	
	/**
	 * @param  bool  $seen
	 * @return Notification
	 */
	public function setSeen(bool $seen): Notification
	{
		$this->seen = $seen;
		
		return $this;
	}
	
	/**
	 * @return bool
	 */
	public function isModNote(): bool
	{
		return $this->isModNote;
	}
	
	/**
	 * @param  bool  $isModNote
	 * @return $this|Notification
	 */
	public function setIsModNote(bool $isModNote): Notification
	{
		$this->isModNote = $isModNote;
		
		return $this;
	}
}
