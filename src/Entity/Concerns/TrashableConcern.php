<?php

declare(strict_types = 1);

namespace App\Entity\Concerns;

use App\Entity\Contracts\TrashableContract;
use App\Entity\User;
use App\Service\FreshTime;
use DateTimeInterface;

/**
 * @property null|DateTimeInterface $trashedAt
 * @property null|User              $trashedBy
 * @property null|DateTimeInterface $restoredAt
 * @property null|User              $restoredBy
 * Trait TrashableConcern
 * @package App\Entity\Concerns
 */
trait TrashableConcern
{
	/**
	 * @return null|DateTimeInterface
	 */
	public function getTrashedAt(): ?DateTimeInterface
	{
		return $this->{$this->getTrashedAtColumn()};
	}
	
	/**
	 * @param  null|DateTimeInterface  $trashedAt
	 * @return $this|TrashableContract
	 */
	public function setTrashedAt(?DateTimeInterface $trashedAt): self
	{
		$this->{$this->getTrashedAtColumn()} = $trashedAt;
		
		return $this;
	}
	
	/**
	 * @return null|string
	 */
	public function getTrashedAtColumn(): ?string
	{
		return defined('static::TRASHED_AT') ? static::TRASHED_AT : 'trashedAt';
	}
	
	public function isTrashed(): bool
	{
		return ! is_null($this->{$this->getTrashedAtColumn()});
	}
	
	/**
	 * Send an entity to trash.
	 * @return $this|TrashableContract
	 */
	public function trash(): self
	{
		$this->setTrashedAt(FreshTime::freshTimestamp());
		
		return $this;
	}
	
	/**
	 * @param  null|User  $trashedBy
	 * @return $this|TrashableContract
	 */
	public function setTrashedBy(?User $trashedBy): self
	{
		$this->trashedBy = $trashedBy;
		
		return $this;
	}
	
	/**
	 * @return null|User
	 */
	public function getTrashedBy(): ?User
	{
		return $this->trashedBy;
	}
	
	/**
	 * @return $this|TrashableContract
	 */
	public function restore(): self
	{
		$this->setTrashedAt(null);
		$this->setTrashedBy(null);
		$this->setRestoredAt(FreshTime::freshTimestamp());
		
		return $this;
	}
	
	public function getRestoredAt(): ?DateTimeInterface
	{
		return $this->{$this->getRestoredAtColumn()};
	}
	
	/**
	 * @param  null|DateTimeInterface  $restoredAt
	 * @return $this|TrashableContract
	 */
	public function setRestoredAt(?DateTimeInterface $restoredAt): self
	{
		$this->{$this->getRestoredAtColumn()} = $restoredAt;
		
		return $this;
	}
	
	/**
	 * @return null|string
	 */
	public function getRestoredAtColumn(): ?string
	{
		return defined('static::RESTORED_AT') ? static::RESTORED_AT : 'restoredAt';
	}
	
	/**
	 * @param  User  $restoredBy
	 * @return $this|TrashableContract
	 */
	public function setRestoredBy(User $restoredBy): self
	{
		$this->restoredBy = $restoredBy;
		
		return $this;
	}
	
	/**
	 * @return null|User
	 */
	public function getRestoredBy(): ?User
	{
		return $this->restoredBy;
	}
}