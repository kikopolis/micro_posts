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
 * @ORM\Entity(repositoryClass="App\Repository\ComplaintRepository")
 */
class Complaint implements AuthorableContract, TimeStampableContract
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
	 * @ORM\Column(type="text", length=2000, nullable=false)
	 * @Assert\NotBlank()
	 * @Assert\Length(
	 *     min="10",
	 *     minMessage="Please enter atleast {{ limit }} characters for the complaint.",
	 *     max="2000",
	 *     maxMessage="Limit of {{ limit }} characters for a complaint."
	 * )
	 * @var null|string
	 */
	protected ?string $body = null;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="complaintsSent")
	 * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
	 * @var null|User
	 */
	protected ?User $author = null;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="complaintsReceived")
	 * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
	 * @var null|User
	 */
	protected ?User $target = null;
	
	/**
	 * note creation and update
	 */
	
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
	 * Complaint constructor.
	 * @param  null|string  $body
	 * @param  null|User    $target
	 * @param  null|User    $author
	 */
	public function __construct(
		?string $body = null,
		?User $target = null,
		?User $author = null
	)
	{
		if (! is_null($body)) {
			
			$this->setBody($body);
		}
		
		if (! is_null($target)) {
			
			$this->setTarget($target);
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
	 * @return $this|Complaint
	 */
	public function setBody(?string $body): Complaint
	{
		$this->body = $this->purify($body);
		
		return $this;
	}
	
	/**
	 * @return null|User
	 */
	public function getTarget(): ?User
	{
		return $this->target;
	}
	
	/**
	 * @param  null|User  $target
	 * @return $this|Complaint
	 */
	public function setTarget(?User $target): Complaint
	{
		$this->target = $target;
		
		return $this;
	}
}
