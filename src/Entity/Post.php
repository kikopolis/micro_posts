<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Entity\Concerns\ApprovableConcern;
use App\Entity\Concerns\AuthorableConcern;
use App\Entity\Concerns\ProfanityConcern;
use App\Entity\Concerns\LikableConcern;
use App\Entity\Concerns\PublishableConcern;
use App\Entity\Concerns\ReportableConcern;
use App\Entity\Concerns\TimeStampableConcern;
use App\Entity\Concerns\TrashableConcern;
use App\Entity\Concerns\ViewableConcern;
use App\Entity\Contracts\ApprovableConctract;
use App\Entity\Contracts\AuthorableContract;
use App\Entity\Contracts\LikableContract;
use App\Entity\Contracts\PublishableContract;
use App\Entity\Contracts\ReportableContract;
use App\Entity\Contracts\TimeStampableContract;
use App\Entity\Contracts\TrashableContract;
use App\Entity\Contracts\ViewableContract;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 * @ORM\Table(indexes={@Index(name="search_idx", columns={"body"})})
 */
class Post implements AuthorableContract, TimeStampableContract, PublishableContract,
                      TrashableContract, LikableContract, ViewableContract,
                      ReportableContract, ApprovableConctract
{
	use ApprovableConcern;
	use AuthorableConcern;
	use LikableConcern;
	use ProfanityConcern;
	use PublishableConcern;
	use ReportableConcern;
	use TimeStampableConcern;
	use TrashableConcern;
	use ViewableConcern;
	
	/** @var int */
	public const PAGINATION_PER_PAGE = 10;
	
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
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
	 * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
	 * @var null|User
	 */
	protected ?User $author = null;
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="post")
	 * @ORM\OrderBy({"createdAt" = "DESC"})
	 * @var Collection
	 */
	protected $comments;
	
	/**
	 * note like
	 */
	
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="postsLiked")
	 * @ORM\JoinTable(name="posts_liked", joinColumns={
	 *          @ORM\JoinColumn(name="post_id", referencedColumnName="id", onDelete="CASCADE")
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
	 * note report
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
	 * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="postsReported")
	 * @ORM\JoinTable(name="posts_reported", joinColumns={
	 *          @ORM\JoinColumn(name="post_id", referencedColumnName="id", onDelete="CASCADE")
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
	 * note approval
	 */
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="postsApproved")
	 * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
	 * @var null|User
	 */
	protected ?User $approvedBy = null;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="postsUnApproved")
	 * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
	 * @var null|User
	 */
	protected ?User $unApprovedBy = null;
	
	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 * @var null|DateTimeInterface
	 */
	protected ?DateTimeInterface $approvedAt = null;
	
	/**
	 * note publish
	 */
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="postsPublished")
	 * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
	 * @var null|User
	 */
	protected ?User $publishedBy = null;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="postsUnPublished")
	 * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
	 * @var null|User
	 */
	protected ?User $unPublishedBy = null;
	
	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 * @var null|DateTimeInterface
	 */
	protected ?DateTimeInterface $publishedAt = null;
	
	/**
	 * note trash
	 */
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="postsTrashed")
	 * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
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
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="postsRestored")
	 * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
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
	 * Post constructor.
	 * @param  null|string  $body
	 * @param  null|User    $author
	 */
	public function __construct(
		?string $body = null,
		?User $author = null
	)
	{
		if (! is_null($body)) {
			
			$this->setBody($body);
		}
		
		if (! is_null($author)) {
			
			$this->setAuthor($author);
		}
		
		$this->comments   = new ArrayCollection();
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
	 * @param  null|string  $body
	 * @return $this|Post
	 */
	public function setBody(?string $body): Post
	{
		$this->body = $body;
		
		return $this;
	}
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getComments(): Collection
	{
		return $this->comments;
	}
}
