<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Entity\Concerns\AuthorableConcern;
use App\Entity\Concerns\TimeStampableConcern;
use App\Entity\Contracts\AuthorableContract;
use App\Entity\Contracts\TimeStampableContract;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Conversation
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\ConversationRepository")
 */
class Conversation implements AuthorableContract, TimeStampableContract
{
	use AuthorableConcern;
	use TimeStampableConcern;
	
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 * @var null|int
	 */
	protected ?int $id = null;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="conversationsCreated")
	 * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
	 * @var null|User
	 */
	protected ?User $author = null;
	
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="conversationsParticipated")
	 * @ORM\JoinTable(name="user_conversations", joinColumns={
	 *          @ORM\JoinColumn(name="conversation_id", referencedColumnName="id", onDelete="CASCADE")
	 *      },
	 *      inverseJoinColumns={
	 *          @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
	 *      }
	 * )
	 * @var ArrayCollection|Collection
	 */
	protected Collection $participants;
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="conversation")
	 * @var ArrayCollection|Collection
	 */
	protected Collection $messages;
	
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
	 * Conversation constructor.
	 */
	public function __construct()
	{
		$this->participants = new ArrayCollection();
		$this->messages     = new ArrayCollection();
	}
	
	/**
	 * @return null|int
	 */
	public function getId(): ?int
	{
		return $this->id;
	}
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getParticipants(): Collection
	{
		return $this->participants;
	}
	
	/**
	 * @param  User  $user
	 * @return $this|Conversation
	 */
	public function addParticipant(User $user): Conversation
	{
		if (! $this->participants->contains($user)) {
			
			$this->participants->add($user);
		}
		
		return $this;
	}
	
	/**
	 * @param  User  $user
	 * @return $this
	 */
	public function removeParticipant(User $user): Conversation
	{
		if ($this->participants->contains($user)) {
			
			$this->participants->removeElement($user);
		}
		
		return $this;
	}
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getMessages()
	{
		return $this->messages;
	}
	
	/**
	 * @param  Message  $message
	 * @return $this|Conversation
	 */
	public function addMessage(Message $message): Conversation
	{
		if (! $this->messages->contains($message)) {
			
			$this->messages->add($message);
		}
		
		return $this;
	}
	
	/**
	 * @param  Message  $message
	 * @return $this|Conversation
	 */
	public function removeMessage(Message $message): Conversation
	{
		if ($this->messages->contains($message)) {
			
			$this->messages->removeElement($message);
		}
		
		return $this;
	}
}
