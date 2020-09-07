<?php

declare(strict_types = 1);

namespace App\Entity\Concerns;

use App\Entity\Contracts\ApprovableConctract;
use App\Entity\User;
use App\Service\FreshTime;
use DateTimeInterface;

/**
 * @property null|DateTimeInterface $approvedAt
 * @property null|User              $approvedBy
 * @property null|User              $unApprovedBy
 * Trait ApprovableConcern
 * @package App\Entity\Concerns
 */
trait ApprovableConcern
{
	/**
	 * @return string
	 */
	public function getApprovedAtColumn(): string
	{
		return defined('static::APPROVED_AT') ? static::APPROVED_AT : 'approvedAt';
	}
	
	/**
	 * @return $this|ApprovableConctract
	 */
	public function approve(): self
	{
		$this->{$this->getApprovedAtColumn()} = FreshTime::freshTimestamp();
		$this->setUnApprovedBy(null);
		
		return $this;
	}
	
	/**
	 * @return $this|ApprovableConctract
	 */
	public function unApprove(): self
	{
		$this->{$this->getApprovedAtColumn()} = null;
		$this->setApprovedBy(null);
		
		return $this;
	}
	
	/**
	 * @return bool
	 */
	public function isApproved(): bool
	{
		return ! is_null($this->{$this->getApprovedAtColumn()});
	}
	
	/**
	 * @return null|DateTimeInterface
	 */
	public function getApprovedAt(): ?DateTimeInterface
	{
		return $this->{$this->getApprovedAtColumn()};
	}
	
	/**
	 * @param  null|User  $approvedBy
	 * @return $this|ApprovableConctract
	 */
	public function setApprovedBy(?User $approvedBy): self
	{
		$this->approvedBy = $approvedBy;
		
		return $this;
	}
	
	/**
	 * @return null|User
	 */
	public function getApprovedBy(): ?User
	{
		return $this->approvedBy;
	}
	
	/**
	 * @param  null|User  $unApprovedBy
	 * @return $this|ApprovableConctract
	 */
	public function setUnApprovedBy(?User $unApprovedBy): self
	{
		$this->unApprovedBy = $unApprovedBy;
		
		return $this;
	}
	
	/**
	 * @return null|User
	 */
	public function getUnApprovedBy(): ?User
	{
		return $this->unApprovedBy;
	}
}