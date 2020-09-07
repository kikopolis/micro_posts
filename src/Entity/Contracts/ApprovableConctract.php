<?php

declare(strict_types = 1);

namespace App\Entity\Contracts;

use App\Entity\User;
use DateTimeInterface;

/**
 * Interface ApprovableConctract
 * @package App\Entity\Contracts
 */
interface ApprovableConctract
{
	/**
	 * @return ApprovableConctract
	 */
	public function approve(): ApprovableConctract;
	
	/**
	 * @return ApprovableConctract
	 */
	public function unApprove(): ApprovableConctract;
	
	/**
	 * @return bool
	 */
	public function isApproved(): bool;
	
	/**
	 * @return null|DateTimeInterface
	 */
	public function getApprovedAt(): ?DateTimeInterface;
	
	/**
	 * @param  null|User  $approvedBy
	 * @return ApprovableConctract
	 */
	public function setApprovedBy(?User $approvedBy): ApprovableConctract;
	
	/**
	 * @return null|User
	 */
	public function getApprovedBy(): ?User;
	
	/**
	 * @param  null|User  $unUnApprovedBy
	 * @return ApprovableConctract
	 */
	public function setUnApprovedBy(?User $unUnApprovedBy): ApprovableConctract;
	
	/**
	 * @return null|User
	 */
	public function getUnApprovedBy(): ?User;
}