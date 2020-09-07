<?php

declare(strict_types = 1);

namespace App\Entity\Concerns;

use App\Entity\Contracts\TimeStampableContract;
use App\Service\FreshTime;
use DateTime;
use DateTimeInterface;

trait TimeStampableConcern
{
	/**
	 * Set usage of timestamps on the entity.
	 * @var bool
	 */
	protected bool $timestamps = true;
	
	/**
	 * @return string
	 */
	public function getCreatedAtColumn(): string
	{
		return defined('static::CREATED_AT') ? static::CREATED_AT : 'createdAt';
	}
	
	/**
	 * @return string
	 */
	public function getUpdatedAtColumn(): string
	{
		return defined('static::UPDATED_AT') ? static::UPDATED_AT : 'updatedAt';
	}
	
	/**
	 * @return DateTimeInterface
	 */
	public function getCreatedAt(): ?DateTimeInterface
	{
		return $this->{$this->getCreatedAtColumn()};
	}
	
	/**
	 * @param  DateTimeInterface  $createdAt
	 * @return $this|TimeStampableContract
	 */
	public function setCreatedAt(DateTimeInterface $createdAt): TimeStampableContract
	{
		$this->{$this->getCreatedAtColumn()} = $createdAt;
		
		return $this;
	}
	
	/**
	 * @return DateTimeInterface
	 */
	public function getUpdatedAt(): ?DateTimeInterface
	{
		return $this->{$this->getUpdatedAtColumn()};
	}
	
	/**
	 * @param  DateTimeInterface  $updatedAt
	 * @return $this|TimeStampableContract
	 */
	public function setUpdatedAt(DateTimeInterface $updatedAt): TimeStampableContract
	{
		$this->{$this->getUpdatedAtColumn()} = $updatedAt;
		
		return $this;
	}
	
	/**
	 * @return $this|TimeStampableContract
	 */
	public function setCreationTimestamps(): TimeStampableContract
	{
		$this->setCreatedAt(FreshTime::freshTimestamp());
		
		return $this;
	}
	
	/**
	 * @return $this|TimeStampableContract
	 */
	public function setUpdatedTimestamps(): TimeStampableContract
	{
		$this->setUpdatedAt(FreshTime::freshTimestamp());
		
		return $this;
	}
	
	/**
	 * Determine if the entity is using timestamps.
	 * @return bool
	 */
	public function hasTimestamps(): bool
	{
		return $this->timestamps;
	}
	
	/**
	 * getCreatedAt and getUpdatedAt are already included by default.
	 * @return array
	 */
	protected function getDates()
	{
		$defaults = [
			static::CREATED_AT,
			static::UPDATED_AT,
		];
		
		return $this->hasTimestamps()
			? array_merge($this->dates, $defaults)
			: $this->dates;
	}
}