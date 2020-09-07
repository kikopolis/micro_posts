<?php

declare(strict_types = 1);

namespace App\Entity\Contracts;

use DateTimeInterface;

interface TimeStampableContract
{
	/**
	 * @return DateTimeInterface
	 */
	public function getCreatedAt(): ?DateTimeInterface;
	
	/**
	 * @param  DateTimeInterface  $createdAt
	 * @return $this|TimeStampableContract
	 */
	public function setCreatedAt(DateTimeInterface $createdAt): TimeStampableContract;
	
	/**
	 * @return DateTimeInterface
	 */
	public function getUpdatedAt(): ?DateTimeInterface;
	
	/**
	 * @param  DateTimeInterface  $updatedAt
	 * @return $this|TimeStampableContract
	 */
	public function setUpdatedAt(DateTimeInterface $updatedAt): TimeStampableContract;
	
	/**
	 * Set timestamps on creation.
	 * @return $this|TimeStampableContract
	 */
	public function setCreationTimestamps(): TimeStampableContract;
	
	/**
	 * Set updated timestamps
	 * @return $this|TimeStampableContract
	 */
	public function setUpdatedTimestamps(): TimeStampableContract;
	
	/**
	 * Determine if the entity is using timestamps.
	 * @return bool
	 */
	public function hasTimestamps(): bool;
}