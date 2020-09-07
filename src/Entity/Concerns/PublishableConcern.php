<?php

declare(strict_types = 1);

namespace App\Entity\Concerns;

use App\Entity\Contracts\PublishableContract;
use App\Entity\User;
use App\Service\FreshTime;
use DateTimeInterface;

/**
 * @property null|DateTimeInterface $publishedAt
 * @property null|User              $publishedBy
 * Trait PublishableConcern
 * @package App\Entity\Concerns
 */
trait PublishableConcern
{
	/**
	 * @return bool
	 */
	public function isPublished(): ?bool
	{
		return ! is_null($this->{$this->getPublishedAtColumn()});
	}
	
	/**
	 * @return null|DateTimeInterface
	 */
	public function getPublishedAt(): ?DateTimeInterface
	{
		return $this->{$this->getPublishedAtColumn()};
	}
	
	/**
	 * @param  null|DateTimeInterface  $publishedAt
	 * @return $this|PublishableContract
	 */
	public function setPublishedAt(?DateTimeInterface $publishedAt): self
	{
		$this->{$this->getPublishedAtColumn()} = $publishedAt;
		
		return $this;
	}
	
	/**
	 * @return null|string
	 */
	public function getPublishedAtColumn(): ?string
	{
		return defined('static::PUBLISHED_AT') ? static::PUBLISHED_AT : 'publishedAt';
	}
	
	/**
	 * Publish an entity.
	 * @return $this|PublishableContract
	 */
	public function publish(): self
	{
		$this->setPublishedAt(FreshTime::freshTimestamp());
		
		return $this;
	}
	
	/**
	 * Un-publish an entity.
	 * @return $this|PublishableContract
	 */
	public function unPublish(): self
	{
		$this->setPublishedAt(null);
		$this->setPublishedBy(null);
		
		return $this;
	}
	
	/**
	 * @return null|User
	 */
	public function getPublishedBy(): ?User
	{
		return $this->publishedBy;
	}
	
	/**
	 * @param  null|User  $publishedBy
	 * @return $this|PublishableContract
	 */
	public function setPublishedBy(?User $publishedBy): self
	{
		$this->publishedBy = $publishedBy;
		
		return $this;
	}
	
	/**
	 * @return null|User
	 */
	public function getUnPublishedBy(): ?User
	{
		return $this->unPublishedBy;
	}
	
	/**
	 * @param  User  $unPublishedBy
	 * @return $this|PublishableContract
	 */
	public function setUnPublishedBy(User $unPublishedBy): self
	{
		$this->unPublishedBy = $unPublishedBy;
		
		return $this;
	}
}