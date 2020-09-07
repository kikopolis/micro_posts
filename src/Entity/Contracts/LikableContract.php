<?php

declare(strict_types = 1);

namespace App\Entity\Contracts;

use App\Entity\User;

/**
 * Interface LikableContract
 * @package App\Entity\Contracts
 */
interface LikableContract
{
	
	/**
	 * @param  User  $likedBy
	 */
	public function like(User $likedBy): void;
	
	/**
	 * @param  User  $likedBy
	 */
	public function unLike(User $likedBy): void;
	
	/**
	 * @return null|int
	 */
	public function getLikeCount(): ?int;
	
	/**
	 * @return null|int
	 */
	public function getWeeklyLikeCount(): ?int;
	
	/**
	 * @return void
	 */
	public function setLikeCounter(): void;
	
	/**
	 * Reset only the weekly counter. No point in gathering all time count if we reset it weekly!
	 * @return void
	 */
	public function resetWeeklyLikeCounter(): void;
}