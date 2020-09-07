<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Entity\Concerns\ProfanityConcern;
use App\Entity\Concerns\SanitizableConcern;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserProfileRepository")
 */
class UserProfile
{
	use ProfanityConcern;
	use SanitizableConcern;
	
	public const DEFAULT_AVATAR = 'public/images/defaultUserAvatar/defaultAvatar.jpg';
	
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="bigint")
	 * @var null|int
	 */
	protected ?int $id = null;
	
	/**
	 * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="profile")
	 * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
	 * @var null|User
	 */
	protected ?User $user = null;
	
	/**
	 * @ORM\Column(type="text", nullable=true, length=500)
	 * @var null|string
	 */
	protected ?string $avatar = self::DEFAULT_AVATAR;
	
	/**
	 * @ORM\Column(type="text", nullable=true)
	 * @Assert\Length(
	 *     max="10000",
	 *     maxMessage="Maximum of {{ limit }} characters for your bio."
	 * )
	 * @var null|string
	 */
	protected ?string $bio = null;
	
	/**
	 * @ORM\Column(type="date", nullable=true)
	 * @var null|DateTimeInterface
	 */
	protected ?DateTimeInterface $birthday = null;
	
	/**
	 * UserProfile constructor.
	 * @param  null|string             $avatar
	 * @param  null|string             $bio
	 * @param  null|DateTimeInterface  $birthday
	 */
	public function __construct(
		?string $avatar = null,
		?string $bio = null,
		?DateTimeInterface $birthday = null
	)
	{
		$this->setAvatar($avatar);
		
		if (! is_null($bio)) {
			
			$this->setBio($bio);
		}
		
		if (! is_null($birthday)) {
			
			$this->setBirthday($birthday);
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
	 * @return null|User
	 */
	public function getUser(): ?User
	{
		return $this->user;
	}
	
	/**
	 * @param  null|User  $user
	 * @return $this|UserProfile
	 */
	public function setUser(?User $user): UserProfile
	{
		if (! $this->getUser()) {
			
			$this->user = $user;
		}
		
		return $this;
	}
	
	/**
	 * @return null|string
	 */
	public function getAvatar(): ?string
	{
		return $this->avatar;
	}
	
	/**
	 * @param  null|string  $avatar
	 * @return $this|UserProfile
	 */
	public function setAvatar(?string $avatar): UserProfile
	{
		if (is_null($avatar)) {
			
			$this->avatar = self::DEFAULT_AVATAR;
		} else {
			
			$this->avatar = $avatar;
		}
		
		return $this;
	}
	
	/**
	 * @return null|string
	 */
	public function getBio(): ?string
	{
		return $this->bio;
	}
	
	/**
	 * @param  null|string  $bio
	 * @return $this|UserProfile
	 */
	public function setBio(?string $bio): UserProfile
	{
		$this->bio = $this->cleanString(
			$this->purify($bio)
		);
		
		return $this;
	}
	
	/**
	 * @return null|DateTimeInterface
	 */
	public function getBirthday(): ?DateTimeInterface
	{
		return $this->birthday;
	}
	
	/**
	 * @param  null|DateTimeInterface  $birthday
	 * @return $this|UserProfile
	 */
	public function setBirthday(?DateTimeInterface $birthday): UserProfile
	{
		$this->birthday = $birthday;
		
		return $this;
	}
}
