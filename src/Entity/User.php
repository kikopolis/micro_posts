<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Entity\Concerns\TimeStampableConcern;
use App\Entity\Concerns\TrashableConcern;
use App\Entity\Contracts\TimeStampableContract;
use App\Entity\Contracts\TrashableContract;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="`user`")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="This e-mail is already in use")
 * @UniqueEntity(fields="username", message="This username is already in use")
 */
class User implements UserInterface, TimeStampableContract, TrashableContract
{
	use TimeStampableConcern;
	use TrashableConcern;
	
	public const ROLE_USER                = 'ROLE_USER';
	
	public const ROLE_MODERATOR           = 'ROLE_MODERATOR';
	
	public const ROLE_ADMINISTRATOR       = 'ROLE_ADMINISTRATOR';
	
	public const ROLE_SUPER_ADMINISTRATOR = 'ROLE_SUPER_ADMINISTRATOR';
	
	public const ROLES                    = [
		self::ROLE_USER,
		self::ROLE_MODERATOR,
		self::ROLE_ADMINISTRATOR,
		self::ROLE_SUPER_ADMINISTRATOR,
	];
	
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="bigint")
	 * @var null|int
	 */
	protected ?int $id = null;
	
	/**
	 * @ORM\Column(type="string", length=50, unique=true, nullable=false)
	 * @Assert\NotBlank()
	 * @Assert\Length(
	 *     min="4",
	 *     max="50",
	 *     minMessage="At least {{ limit }} characters required for username.",
	 *     maxMessage="Maximum length for username is {{ limit }} characters."
	 *     )
	 * @Assert\Regex(
	 *     pattern="/^[a-zA-Z][a-zA-Z0-9_]+[a-zA-Z0-9]$/",
	 *     message="Username must contain only letters, numbers or underscore.
	 *     Additionally, username must begin with a letter and end with either a letter or a number."
	 *     )
	 * @var null|string
	 */
	protected ?string $username = null;
	
	/**
	 * @ORM\Column(type="text", length=8096, nullable=false)
	 * @var null|string
	 */
	protected ?string $password = null;
	
	/**
	 * @Assert\NotBlank(message="Password cannot be blank.")
	 * @Assert\Length(
	 *     max="8096",
	 *     maxMessage="Password cannot be longer than {{ limit }} characters."
	 * )
	 * @Assert\Regex(
	 *     pattern="/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{8,}$/",
	 *     message="Minimum length is 8. The password must also contain one uppercase, one lowercase letter and one digit."
	 * )
	 * @var null|string
	 */
	protected ?string $plainPassword = null;
	
	/**
	 * @Assert\NotBlank(message="Repeat password cannot be blank.")
	 * @Assert\Expression(
	 *     expression="this.getPlainPassword() === this.getRetypedPlainPassword()",
	 *     message="Passwords do not match. Please type them again."
	 *     )
	 * @var null|string
	 */
	protected ?string $retypedPlainPassword = null;
	
	/**
	 * @ORM\Column(type="string", length=254, unique=true, nullable=false)
	 * @Assert\NotBlank()
	 * @Assert\Email()
	 * @Assert\Length(
	 *     min="5",
	 *     minMessage="Email must be atleast {{ limit }} characters long.",
	 *     max="254",
	 *     maxMessage="Email cannot exceed {{ limit }} characters."
	 * )
	 * @var null|string
	 */
	protected ?string $email = null;
	
	/**
	 * @ORM\Column(type="string", length=254, nullable=true)
	 * @Assert\Email()
	 * @Assert\Length(
	 *     min="5",
	 *     minMessage="Email must be atleast {{ limit }} characters long.",
	 *     max="254",
	 *     maxMessage="Email cannot exceed {{ limit }} characters."
	 * )
	 * @var null|string
	 */
	protected ?string $oldEmail = null;
	
	/**
	 * @ORM\Column(type="string", length=150, nullable=false)
	 * @Assert\NotBlank()
	 * @Assert\Length(
	 *     min="4",
	 *     max="150",
	 *     minMessage="Atleast {{ limit }} characters required for full name.",
	 *     maxMessage="Maximum length for full name is {{ limit }} characters.")
	 * @Assert\Regex(
	 *     pattern="/^[a-zA-Z][a-zA-Z ]+[a-zA-Z]$/",
	 *     message="Full name can only contain letters and spaces and cannot begin or end with a space"
	 * )
	 */
	protected ?string $fullName = null;
	
	/**
	 * @ORM\Column(type="array", nullable=false)
	 * @var array
	 */
	protected array $roles = [self::ROLE_USER];
	
	/**
	 * note token
	 */
	
	/**
	 * @ORM\Column(type="string", nullable=true, length=64)
	 * @var null|string
	 */
	protected ?string $accountActivationToken = null;
	
	/**
	 * @ORM\Column(type="string", nullable=true, length=64)
	 * @var null|string
	 */
	protected ?string $passwordResetToken = null;
	
	/**
	 * note security
	 */
	
	/**
	 * @ORM\Column(type="boolean", nullable=false)
	 * @var bool
	 */
	protected bool $activated = false;
	
	/**
	 * If the user is disabled by an admin.
	 * @ORM\Column(type="boolean", nullable=false)
	 * @var boolean
	 */
	protected bool $disabled = false;
	
	/**
	 * @ORM\Column(type="boolean", nullable=false)
	 * @var boolean
	 */
	protected bool $forcedPasswordChange = false;
	
	/**
	 * note profile and preferences
	 */
	
	/**
	 * @ORM\OneToOne(targetEntity="App\Entity\UserPreferences", mappedBy="user", cascade={"persist"})
	 * @var null|UserPreferences
	 */
	protected ?UserPreferences $preferences = null;
	
	/**
	 * @ORM\OneToOne(targetEntity="App\Entity\UserProfile", mappedBy="user", cascade={"persist"})
	 * @var null|UserProfile
	 */
	protected ?UserProfile $profile = null;
	
	/**
	 * note followers
	 */
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="following")
	 * @var ArrayCollection|Collection
	 */
	protected Collection $followers;
	
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="followers")
	 * @ORM\JoinTable(name="following", joinColumns={
	 *          @ORM\JoinColumn(name="follower_id", referencedColumnName="id", onDelete="CASCADE")
	 *      },
	 *      inverseJoinColumns={
	 *          @ORM\JoinColumn(name="following_id", referencedColumnName="id", onDelete="CASCADE")
	 *      }
	 * )
	 * @var ArrayCollection|Collection
	 */
	protected Collection $following;
	
	/**
	 * note post
	 */
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="author")
	 * @ORM\OrderBy({"createdAt" = "DESC"})
	 * @var ArrayCollection|Collection
	 */
	protected Collection $posts;
	
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\Post", mappedBy="likedBy")
	 * @var ArrayCollection|Collection
	 */
	protected Collection $postsLiked;
	
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\Post", mappedBy="reportedBy")
	 * @var ArrayCollection|Collection
	 */
	protected Collection $postsReported;
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="approvedBy")
	 * @var ArrayCollection|Collection
	 */
	protected Collection $postsApproved;
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="unApprovedBy")
	 * @var ArrayCollection|Collection
	 */
	protected Collection $postsUnApproved;
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="publishedBy")
	 * @var ArrayCollection|Collection
	 */
	protected Collection $postsPublished;
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="unPublishedBy")
	 * @var ArrayCollection|Collection
	 */
	protected Collection $postsUnPublished;
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="trashedBy")
	 * @var ArrayCollection|Collection
	 */
	protected Collection $postsTrashed;
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="restoredBy")
	 * @var ArrayCollection|Collection
	 */
	protected Collection $postsRestored;
	
	/**
	 * note comments
	 */
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="author")
	 * @ORM\OrderBy({"createdAt" = "DESC"})
	 * @var ArrayCollection|Collection
	 */
	protected Collection $comments;
	
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\Comment", mappedBy="likedBy")
	 * @var ArrayCollection|Collection
	 */
	protected Collection $commentsLiked;
	
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\Comment", mappedBy="reportedBy")
	 * @var ArrayCollection|Collection
	 */
	protected Collection $commentsReported;
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="approvedBy")
	 * @var ArrayCollection|Collection
	 */
	protected Collection $commentsApproved;
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="unApprovedBy")
	 * @var ArrayCollection|Collection
	 */
	protected Collection $commentsUnApproved;
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="trashedBy")
	 * @var ArrayCollection|Collection
	 */
	protected Collection $commentsTrashed;
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="restoredBy")
	 * @var ArrayCollection|Collection
	 */
	protected Collection $commentsRestored;
	
	/**
	 * note notifications
	 */
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Notification", mappedBy="owner")
	 * @var ArrayCollection|Collection
	 */
	protected Collection $notifications;
	
	/**
	 * note messaging and conversations
	 */
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="author")
	 * @var ArrayCollection|Collection
	 */
	protected Collection $messages;
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Conversation", mappedBy="author")
	 * @var ArrayCollection|Collection
	 */
	protected Collection $conversationsCreated;
	
	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\Conversation", mappedBy="participants")
	 * @var ArrayCollection|Collection
	 */
	protected Collection $conversationsParticipated;
	
	/**
	 * note complaints
	 */
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Complaint", mappedBy="author")
	 * @var ArrayCollection|Collection
	 */
	protected Collection $complaintsSent;
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Complaint", mappedBy="target")
	 * @var ArrayCollection|Collection
	 */
	protected Collection $complaintsReceived;
	
	/**
	 * note trash
	 */
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="usersTrashed")
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
	 * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="trashedBy")
	 * @var ArrayCollection|Collection
	 */
	protected Collection $usersTrashed;
	
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
	
	public function __toString(): string
	{
		return "$this->username + $this->email";
	}
	
	/**
	 * User constructor.
	 * @param  null|string           $username
	 * @param  null|string           $email
	 * @param  null|string           $fullName
	 * @param  null|string           $plainPassword
	 * @param  null|string           $retypedPlainPassword
	 * @param  null|UserPreferences  $preferences
	 * @param  null|UserProfile      $profile
	 */
	public function __construct(
		?string $username = null,
		?string $email = null,
		?string $fullName = null,
		?string $plainPassword = null,
		?string $retypedPlainPassword = null,
		?UserPreferences $preferences = null,
		?UserProfile $profile = null
	)
	{
		if (! is_null($username)) {
			
			$this->setUsername($username);
		}
		
		if (! is_null($email)) {
			
			$this->setEmail($email);
		}
		
		if (! is_null($fullName)) {
			
			$this->setFullName($fullName);
		}
		
		if (! is_null($plainPassword)) {
			
			$this->setPlainPassword($plainPassword);
		}
		
		if (! is_null($retypedPlainPassword)) {
			
			$this->setRetypedPlainPassword($retypedPlainPassword);
		}
		
		if (! is_null($preferences)) {
			
			$this->setPreferences($preferences);
		}
		
		if (! is_null($profile)) {
			
			$this->setProfile($profile);
		}
		
		$this->following                 = new ArrayCollection();
		$this->followers                 = new ArrayCollection();
		$this->posts                     = new ArrayCollection();
		$this->postsLiked                = new ArrayCollection();
		$this->postsReported             = new ArrayCollection();
		$this->postsApproved             = new ArrayCollection();
		$this->postsUnApproved           = new ArrayCollection();
		$this->postsPublished            = new ArrayCollection();
		$this->postsUnPublished          = new ArrayCollection();
		$this->postsTrashed              = new ArrayCollection();
		$this->postsRestored             = new ArrayCollection();
		$this->comments                  = new ArrayCollection();
		$this->commentsLiked             = new ArrayCollection();
		$this->commentsReported          = new ArrayCollection();
		$this->commentsApproved          = new ArrayCollection();
		$this->commentsUnApproved        = new ArrayCollection();
		$this->commentsTrashed           = new ArrayCollection();
		$this->commentsRestored          = new ArrayCollection();
		$this->notifications             = new ArrayCollection();
		$this->messages                  = new ArrayCollection();
		$this->conversationsCreated      = new ArrayCollection();
		$this->conversationsParticipated = new ArrayCollection();
		$this->usersTrashed              = new ArrayCollection();
		$this->complaintsSent            = new ArrayCollection();
		$this->complaintsReceived        = new ArrayCollection();
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
	public function getUsername(): ?string
	{
		return $this->username;
	}
	
	/**
	 * @param  null|string  $username
	 * @return $this|User
	 */
	public function setUsername(?string $username): User
	{
		// Do not allow changing of username once set
		if (! $this->getUsername()) {
			
			$this->username = $username;
		}
		
		return $this;
	}
	
	/**
	 * @return null|string
	 */
	public function getPassword(): ?string
	{
		return $this->password;
	}
	
	/**
	 * @param  string  $password
	 * @return $this|User
	 */
	public function setPassword(string $password): User
	{
		$this->password = $password;
		
		return $this;
	}
	
	/**
	 * @return null|string
	 */
	public function getPlainPassword(): ?string
	{
		return $this->plainPassword;
	}
	
	/**
	 * @param  null|string  $plainPassword
	 * @return $this|User
	 */
	public function setPlainPassword(?string $plainPassword): User
	{
		$this->plainPassword = $plainPassword;
		
		return $this;
	}
	
	/**
	 * @return null|string
	 */
	public function getRetypedPlainPassword(): ?string
	{
		return $this->retypedPlainPassword;
	}
	
	/**
	 * @param  null|string  $retypedPlainPassword
	 * @return $this|User
	 */
	public function setRetypedPlainPassword(?string $retypedPlainPassword): User
	{
		$this->retypedPlainPassword = $retypedPlainPassword;
		
		return $this;
	}
	
	/**
	 * Erase...
	 */
	public function eraseCredentials(): void
	{
		$this->setPlainPassword(null);
		$this->setRetypedPlainPassword(null);
	}
	
	/**
	 * @return null|string
	 */
	public function getSalt(): ?string
	{
		return null;
	}
	
	/**
	 * @return null|string
	 */
	public function getEmail(): ?string
	{
		return $this->email;
	}
	
	/**
	 * @param  null|string  $email
	 * @return $this|User
	 */
	public function setEmail(?string $email): User
	{
		if ($email !== $this->email) {
			
			$this->setOldEmail($this->email);
		}
		
		$this->email = $email;
		
		return $this;
	}
	
	/**
	 * @return null|string
	 */
	public function getOldEmail(): ?string
	{
		return $this->oldEmail;
	}
	
	/**
	 * @param  null|string  $oldEmail
	 * @return $this|User
	 */
	public function setOldEmail(?string $oldEmail): User
	{
		$this->oldEmail = $oldEmail;
		
		return $this;
	}
	
	/**
	 * @return null|string
	 */
	public function getFullName(): ?string
	{
		return $this->fullName;
	}
	
	/**
	 * @param  null|string  $fullName
	 * @return $this|User
	 */
	public function setFullName(?string $fullName): User
	{
		$this->fullName = $fullName;
		
		return $this;
	}
	
	/**
	 * @return array
	 */
	public function getRoles(): array
	{
		return $this->roles;
	}
	
	/**
	 * @param  string  $role
	 * @return bool
	 */
	public function hasRole(string $role): bool
	{
		if (in_array($role, self::ROLES)) {
			
			return in_array($role, $this->getRoles());
		}
		
		return false;
	}
	
	/**
	 * @param  string  $role
	 * @return User
	 */
	public function addRole(string $role): User
	{
		if (in_array($role, self::ROLES)) {
			
			if (! in_array($role, $this->roles)) {
				
				$this->roles = array_merge($this->getRoles(), [$role]);
			}
		}
		
		return $this;
	}
	
	public function removeRole(string $role): User
	{
		if (in_array($role, self::ROLES)
			&& in_array($role, $this->roles)) {
			
			$this->roles = array_filter(
				$this->roles,
				fn($userRole): bool => $role !== $userRole
			);
		}
		
		return $this;
	}
	
	/**
	 * @return null|string
	 */
	public function getAccountActivationToken(): ?string
	{
		return $this->accountActivationToken;
	}
	
	/**
	 * @param  null|string  $accountActivationToken
	 * @return $this|User
	 */
	public function setAccountActivationToken(?string $accountActivationToken): User
	{
		$this->accountActivationToken = $accountActivationToken;
		
		return $this;
	}
	
	/**
	 * @return null|string
	 */
	public function getPasswordResetToken(): ?string
	{
		return $this->passwordResetToken;
	}
	
	/**
	 * @param  null|string  $passwordResetToken
	 * @return $this|User
	 */
	public function setPasswordResetToken(?string $passwordResetToken): User
	{
		$this->passwordResetToken = $passwordResetToken;
		
		return $this;
	}
	
	/**
	 * @return bool
	 */
	public function isActivated(): bool
	{
		return $this->activated;
	}
	
	/**
	 * @return $this
	 */
	public function activate(): User
	{
		$this->activated = true;
		
		return $this;
	}
	
	/**
	 * @return $this
	 */
	public function deActivate(): User
	{
		$this->activated = false;
		
		return $this;
	}
	
	/**
	 * @return bool
	 */
	public function isDisabled(): bool
	{
		return $this->disabled;
	}
	
	/**
	 * @return User
	 */
	public function disable(): User
	{
		$this->disabled = true;
		
		return $this;
	}
	
	/**
	 * @return User
	 */
	public function enable(): User
	{
		$this->disabled = false;
		
		return $this;
	}
	
	/**
	 * @return bool
	 */
	public function isForcedPasswordChange(): bool
	{
		return $this->forcedPasswordChange;
	}
	
	/**
	 * @param  bool  $set
	 * @return $this
	 */
	public function setForcePasswordChange(bool $set): User
	{
		$this->forcedPasswordChange = $set;
		
		return $this;
	}
	
	/**
	 * @return null|UserPreferences
	 */
	public function getPreferences(): ?UserPreferences
	{
		return $this->preferences;
	}
	
	/**
	 * @param  null|UserPreferences  $preferences
	 * @return $this|User
	 */
	public function setPreferences(?UserPreferences $preferences): User
	{
		// prevent changing preferences once set
		if (! $this->getPreferences()) {
			
			$this->preferences = $preferences;
			
			$preferences->setUser($this);
		}
		
		return $this;
	}
	
	/**
	 * @return null|UserProfile
	 */
	public function getProfile(): ?UserProfile
	{
		return $this->profile;
	}
	
	/**
	 * @param  null|UserProfile  $profile
	 * @return $this|User
	 */
	public function setProfile(?UserProfile $profile): User
	{
		// prevent setting a new profile once set
		if (! $this->getProfile()) {
			
			$this->profile = $profile;
			
			$profile->setUser($this);
		}
		
		return $this;
	}
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getFollowers()
	{
		return $this->followers;
	}
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getFollowing()
	{
		return $this->following;
	}
	
	/**
	 * @param  User  $userToFollow
	 * @return User
	 */
	public function follow(User $userToFollow): User
	{
		if (! $this->following->contains($userToFollow)) {
			
			$this->following->add($userToFollow);
			
			$userToFollow->getFollowers()->add($this);
		}
		
		return $this;
	}
	
	/**
	 * @param  User  $userToUnFollow
	 * @return User
	 */
	public function unFollow(User $userToUnFollow): User
	{
		if ($this->following->contains($userToUnFollow)) {
			
			$this->following->removeElement($userToUnFollow);
			
			$userToUnFollow->getFollowers()->removeElement($this);
		}
		
		return $this;
	}
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getPosts(): Collection
	{
		return $this->posts;
	}
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getPostsLiked(): Collection
	{
		return $this->postsLiked;
	}
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getPostsReported(): Collection
	{
		return $this->postsReported;
	}
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getPostsApproved(): Collection
	{
		return $this->postsApproved;
	}
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getPostsUnApproved(): Collection
	{
		return $this->postsUnApproved;
	}
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getPostsPublished(): Collection
	{
		return $this->postsPublished;
	}
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getPostsUnPublished(): Collection
	{
		return $this->postsUnPublished;
	}
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getPostsTrashed(): Collection
	{
		return $this->postsTrashed;
	}
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getPostsRestored(): Collection
	{
		return $this->postsRestored;
	}
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getComments(): Collection
	{
		return $this->comments;
	}
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getCommentsLiked(): Collection
	{
		return $this->commentsLiked;
	}
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getCommentsReported(): Collection
	{
		return $this->commentsReported;
	}
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getCommentsApproved(): Collection
	{
		return $this->commentsApproved;
	}
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getCommentsUnApproved(): Collection
	{
		return $this->commentsUnApproved;
	}
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getCommentsTrashed(): Collection
	{
		return $this->commentsTrashed;
	}
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getCommentsRestored()
	{
		return $this->commentsRestored;
	}
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getNotifications(): Collection
	{
		return $this->notifications;
	}
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getMessages(): Collection
	{
		return $this->messages;
	}
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getConversationsCreated(): Collection
	{
		return $this->conversationsCreated;
	}
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getConversationsParticipated(): Collection
	{
		return $this->conversationsParticipated;
	}
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getUsersTrashed(): Collection
	{
		return $this->usersTrashed;
	}
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getComplaintsSent(): Collection
	{
		return $this->complaintsSent;
	}
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getComplaintsReceived(): Collection
	{
		return $this->complaintsReceived;
	}
}
