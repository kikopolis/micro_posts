<?php

declare(strict_types = 1);

namespace App\Entity\Contracts;

use App\Entity\User;
use DateTimeInterface;

interface TrashableContract
{
	/**
	 * @param  null|DateTimeInterface  $trashedAt
	 * @return $this|TrashableContract
	 */
	public function setTrashedAt(?DateTimeInterface $trashedAt): TrashableContract;
	
	/**
	 * @return null|DateTimeInterface
	 */
	public function getTrashedAt(): ?DateTimeInterface;
	
	/**
	 * @return null|string
	 */
	public function getTrashedAtColumn(): ?string;
	
	/**
	 * @param  null|DateTimeInterface  $restoredAt
	 * @return $this|TrashableContract
	 */
	public function setRestoredAt(?DateTimeInterface $restoredAt): TrashableContract;
	
	/**
	 * @return null|DateTimeInterface
	 */
	public function getRestoredAt(): ?DateTimeInterface;
	
	/**
	 * @return null|string
	 */
	public function getRestoredAtColumn(): ?string;
	
	/**
	 * Send an entity to trash.
	 * @return $this|TrashableContract
	 */
	public function trash(): TrashableContract;
	
	/**
	 * @return null|User
	 */
	public function getTrashedBy(): ?User;
	
	/**
	 * @param  User  $trashedBy
	 * @return TrashableContract
	 */
	public function setTrashedBy(User $trashedBy): TrashableContract;
	
	/**
	 * @return $this|TrashableContract
	 */
	public function restore(): TrashableContract;
	
	/**
	 * @return null|User
	 */
	public function getRestoredBy(): ?User;
	
	/**
	 * @param  User  $restoredBy
	 * @return TrashableContract
	 */
	public function setRestoredBy(User $restoredBy): TrashableContract;
	
	/**
	 * Determine if Entity has been soft deleted.
	 * @return bool
	 */
	public function isTrashed(): bool;
}