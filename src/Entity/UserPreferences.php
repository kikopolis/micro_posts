<?php

declare(strict_types = 1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserPreferencesRepository")
 */
class UserPreferences
{
	/** @var int */
	public const SORT_BY_ALL_POSTS_NEWEST_FIRST = 0;
	
	/** @var int */
	public const SORT_BY_FOLLOWED_USERS_NEWEST_FIRST = 1;
	
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="bigint")
	 * @var null|int
	 */
	protected ?int $id = null;
	
	/**
	 * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="preferences")
	 * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
	 * @var null|User
	 */
	protected ?User $user = null;
	
	/**
	 * @ORM\Column(type="string", length=8, nullable=false)
	 * @Assert\Length(
	 *     max="2",
	 *     maxMessage="Locale cannot be longer than {{ limit }} characters."
	 * )
	 * @var null|string
	 */
	protected ?string $locale = null;
	
	/**
	 * @ORM\Column(type="integer", nullable=false)
	 * @var int
	 */
	protected int $sortHomePageBy = self::SORT_BY_ALL_POSTS_NEWEST_FIRST;
	
	/**
	 * UserPreferences constructor.
	 * @param  string  $locale
	 * @param  int     $sortHomePageBy
	 */
	public function __construct(
		string $locale = 'en',
		int $sortHomePageBy = self::SORT_BY_ALL_POSTS_NEWEST_FIRST
	)
	{
		$this->locale         = $locale;
		$this->sortHomePageBy = $sortHomePageBy;
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
	 * @return $this|UserPreferences
	 */
	public function setUser(?User $user): UserPreferences
	{
		if (! $this->getUser()) {
			
			$this->user = $user;
		}
		
		return $this;
	}
	
	/**
	 * @return null|string
	 */
	public function getLocale(): ?string
	{
		return $this->locale;
	}
	
	/**
	 * @param  null|string  $locale
	 * @return $this|UserPreferences
	 */
	public function setLocale(?string $locale): UserPreferences
	{
		$this->locale = $locale;
		
		return $this;
	}
	
	/**
	 * @return null|int
	 */
	public function getSortHomePageBy(): ?int
	{
		return $this->sortHomePageBy;
	}
	
	/**
	 * @param  null|int  $sortHomePageBy
	 * @return $this|UserPreferences
	 */
	public function setSortHomePageBy(?int $sortHomePageBy): UserPreferences
	{
		$this->sortHomePageBy = $sortHomePageBy;
		
		return $this;
	}
}
