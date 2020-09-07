<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Entity\Concerns\ApprovableConcern;
use App\Entity\Concerns\AuthorableConcern;
use App\Entity\Concerns\ProfanityConcern;
use App\Entity\Concerns\ReportableConcern;
use App\Entity\Concerns\SanitizableConcern;
use App\Entity\Concerns\LikableConcern;
use App\Entity\Concerns\TimeStampableConcern;
use App\Entity\Concerns\TrashableConcern;
use App\Entity\Concerns\ViewableConcern;
use App\Entity\Contracts\ApprovableConctract;
use App\Entity\Contracts\AuthorableContract;
use App\Entity\Contracts\LikableContract;
use App\Entity\Contracts\ReportableContract;
use App\Entity\Contracts\TimeStampableContract;
use App\Entity\Contracts\TrashableContract;
use App\Entity\Contracts\ViewableContract;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 * @ORM\Table(indexes={@Index(name="search_idx", columns={"body"})})
 */
class Comment implements
	AuthorableContract, TimeStampableContract, LikableContract,
	TrashableContract, ViewableContract, ReportableContract,
	ApprovableConctract
{
	use ApprovableConcern;
	use AuthorableConcern;
	use LikableConcern;
	use ProfanityConcern;
	use ReportableConcern;
	use SanitizableConcern;
	use TimeStampableConcern;
	use TrashableConcern;
	use ViewableConcern;
	
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="bigint")
	 * @var null|int
	 */
	protected ?int $id = null;
	
	/**
	 * @ORM\Column(type="string", length=2024, nullable=false)
	 * @Assert\NotBlank()
	 * @Assert\Length(
	 *     min="10",
	 *     max="280",
	 *     minMessage="Please enter a minimum of {{ limit }} characters!",
	 *     maxMessage="No more than {{ limit }} characters allowed!"
	 * )
	 * @var null|string
	 */
	protected ?string $body = null;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
	 * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
	 * @var null|User|UserInterface
	 */
	protected ?User $author = null;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Post", inversedBy="comments")
	 * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
	 * @var null|Post
	 */
	protected ?Post $post = null;
	
	/**
	 * note reporting
	 */
	
	/**
	 * @ORM\Column(type="boolean", nullable=false)
	 * @var bool
	 */
	protected bool $reported = false;
	
	/**
	 * @ORM\Column(type="integer", nullable=false)
	 * @var null|int
	 */
	protected ?int $reportCount = 0;
	
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="commentsReported")
	 * @ORM\JoinTable(name="comments_reported", joinColumns={
	 *          @ORM\JoinColumn(name="comment_id", referencedColumnName="id", onDelete="CASCADE")
	 *      },
	 *      inverseJoinColumns={
	 *          @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
	 *      }
	 * )
	 * @var Collection|ArrayCollection
	 */
	protected Collection $reportedBy;
	
	/**
	 * note view
	 */
	
	/**
	 * @ORM\Column(type="bigint", nullable=false)
	 * @var int
	 */
	protected int $viewCount = 0;
	
	/**
	 * @ORM\Column(type="bigint", nullable=false)
	 * @var int
	 */
	protected int $weeklyViewCount = 0;
	
	/**
	 * @ORM\Column(type="bigint", nullable=false)
	 * @var int
	 */
	protected int $monthlyViewCount = 0;
	
	/**
	 * note Like
	 */
	
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="commentsLiked")
	 * @ORM\JoinTable(name="comments_liked", joinColumns={
	 *          @ORM\JoinColumn(name="comment_id", referencedColumnName="id", onDelete="CASCADE")
	 *      },
	 *      inverseJoinColumns={
	 *          @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
	 *      }
	 * )
	 * @var Collection|ArrayCollection
	 */
	protected Collection $likedBy;
	
	/**
	 * @ORM\Column(type="bigint", nullable=false)
	 * @var int
	 */
	protected int $likeCount = 0;
	
	/**
	 * @ORM\Column(type="bigint", nullable=false)
	 * @var int
	 */
	protected int $weeklyLikeCount = 0;
	
	/**
	 * note approval
	 */
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="commentsApproved")
	 * @ORM\JoinColumn(nullable=true)
	 * @var null|User
	 */
	protected ?User $approvedBy = null;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="commentsUnApproved")
	 * @ORM\JoinColumn(nullable=true)
	 * @var null|User
	 */
	protected ?User $unApprovedBy = null;
	
	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 * @var null|DateTimeInterface
	 */
	protected ?DateTimeInterface $approvedAt = null;
	
	/**
	 * note trash
	 */
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="commentsTrashed")
	 * @ORM\JoinColumn(nullable=true)
	 * @var null|User
	 */
	protected ?User $trashedBy = null;
	
	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 * @var null|DateTimeInterface
	 */
	protected ?DateTimeInterface $trashedAt = null;
	
	/**
	 * note restore
	 */
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="commentsRestored")
	 * @ORM\JoinColumn(nullable=true)
	 * @var null|User
	 */
	protected ?User $restoredBy = null;
	
	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 * @var null|DateTimeInterface
	 */
	protected ?DateTimeInterface $restoredAt = null;
	
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
	 * Comment constructor.
	 * @param   null|string   $body
	 * @param   null|Post     $post
	 * @param   null|User     $author
	 */
	public function __construct(
		?string $body = null,
		?Post $post = null,
		?User $author = null
	)
	{
		if (! is_null($body)) {
			
			$this->setBody($body);
		}
		
		if (! is_null($post)) {
			
			$this->setPost($post);
		}
		
		if (! is_null($author)) {
			
			$this->setAuthor($author);
		}
		
		$this->likedBy    = new ArrayCollection();
		$this->reportedBy = new ArrayCollection();
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
	 * @param   null|string   $body
	 * @return $this|Comment
	 */
	public function setBody(?string $body): Comment
	{
		//		$this->body = $this->cleanString(
		//			$this->sanitize($body)
		//		);
		
		$this->body = $body;
		
		return $this;
	}
	
	/**
	 * @return null|Post
	 */
	public function getPost(): ?Post
	{
		return $this->post;
	}
	
	/**
	 * @param   null|Post   $post
	 * @return $this|Comment
	 */
	public function setPost(?Post $post): Comment
	{
		$this->post = $post;
		
		return $this;
	}
}
