<?php

declare(strict_types = 1);

namespace App\Entity\Contracts;

use App\Entity\User;
use DateTimeInterface;

interface PublishableContract
{
	/**
	 * @return null|bool
	 */
	public function isPublished(): ?bool;
	
	/**
	 * @return PublishableContract
	 */
	public function publish(): PublishableContract;
	
	/**
	 * @return PublishableContract
	 */
	public function unPublish(): PublishableContract;
	
	/**
	 * @return null|DateTimeInterface
	 */
	public function getPublishedAt(): ?DateTimeInterface;
	
	/**
	 * @param  DateTimeInterface  $publishedAt
	 * @return $this|PublishableContract
	 */
	public function setPublishedAt(DateTimeInterface $publishedAt): PublishableContract;
	
	/**
	 * @return null|User
	 */
	public function getPublishedBy(): ?User;
	
	/**
	 * @param  User  $publishedBy
	 * @return PublishableContract
	 */
	public function setPublishedBy(User $publishedBy): PublishableContract;
}