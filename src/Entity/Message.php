<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Entity\Concerns\AuthorableConcern;
use App\Entity\Concerns\SanitizableConcern;
use App\Entity\Concerns\TimeStampableConcern;
use App\Entity\Contracts\AuthorableContract;
use App\Entity\Contracts\TimeStampableContract;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 */
class Message implements AuthorableContract, TimeStampableContract
{
	use AuthorableConcern;
	use SanitizableConcern;
	use TimeStampableConcern;
	
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="bigint")
	 * @var null|int
	 */
	protected ?int $id = null;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="messages")
	 * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
	 * @var null|User
	 */
	protected ?User $author = null;
	
	/**
	 * @ORM\Column(type="text", length=500, nullable=false)
	 * @Assert\NotBlank()
	 * @Assert\Length(
	 *     min="1",
	 *     minMessage="Required at least {{ limit }} characters.",
	 *     max="2500",
	 *     maxMessage="Maximum message length is {{ limit }} characters."
	 * )
	 * @var null|string
	 */
	protected ?string $body = null;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Conversation", inversedBy="messages")
	 * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
	 * @var null|Conversation
	 */
	protected ?Conversation $conversation = null;
	
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
	 * Message constructor.
	 * @param  null|string        $body
	 * @param  null|Conversation  $conversation
	 * @param  null|User          $author
	 */
	public function __construct(
		?string $body = null,
		?Conversation $conversation = null,
		?User $author = null
	)
	{
		if (! is_null($body)) {
			
			$this->setBody($body);
		}
		
		if (! is_null($conversation)) {
			
			$this->setConversation($conversation);
		}
		
		if (! is_null($author)) {
			
			$this->setAuthor($author);
		}
	}
	
	/**
	 * @return null|int
	 */
	public function getId(): ?int
	{
		return $this->id;
	}
	
	/**
	 * @return null|string
	 */
	public function getBody(): ?string
	{
		return $this->body;
	}
	
	/**
	 * @param  null|string  $body
	 * @return $this|Message
	 */
	public function setBody(?string $body): Message
	{
		$this->body = $this->sanitize($body);
		
		return $this;
	}
	
	/**
	 * @return null|Conversation
	 */
	public function getConversation(): ?Conversation
	{
		return $this->conversation;
	}
	
	/**
	 * @param  null|Conversation  $conversation
	 * @return $this|Message
	 */
	public function setConversation(?Conversation $conversation): Message
	{
		$this->conversation = $conversation;
		
		return $this;
	}
}
