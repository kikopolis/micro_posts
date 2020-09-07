<?php

declare(strict_types = 1);

namespace App\Entity\Concerns;

use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @property Collection $likedBy
 * @property int        $likeCount
 * @property int        $weeklyLikeCount
 * Trait LikableConcern
 * @package App\Entity\Concerns
 */
trait LikableConcern
{
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getLikedBy(): Collection
	{
		return $this->likedBy;
	}
	
	/**
	 * Like a post if not already liked.
	 * @param   User   $user
	 */
	public function like(User $user): void
	{
		$this->likedBy->add($user);
		
		$this->weeklyLikeCount++;
		
		$this->setLikeCounter();
	}
	
	/**
	 * Dislike a post if previously liked.
	 * @param   User   $user
	 */
	public function unlike(User $user): void
	{
		$this->likedBy->removeElement($user);
		
		$this->weeklyLikeCount--;
		
		$this->setLikeCounter();
	}
	
	/**
	 * @return null|int
	 */
	public function getLikeCount(): ?int
	{
		return $this->likeCount;
	}
	
	/**
	 * @return null|int
	 */
	public function getWeeklyLikeCount(): ?int
	{
		return $this->weeklyLikeCount;
	}
	
	/**
	 * @return void
	 */
	public function setLikeCounter(): void
	{
		$this->likeCount = $this->getLikedBy()->count();
	}
	
	/**
	 * @return void
	 */
	public function resetWeeklyLikeCounter(): void
	{
		$this->weeklyLikeCount = 0;
	}
}