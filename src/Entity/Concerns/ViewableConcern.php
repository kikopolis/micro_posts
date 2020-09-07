<?php

declare(strict_types = 1);

namespace App\Entity\Concerns;

/**
 * @property int $viewCount
 * @property int $weeklyViewCount
 * @property int $monthlyViewCount
 * Trait ViewableConcern
 * @package App\Entity\Concerns
 */
trait ViewableConcern
{
	/**
	 * @return null|int
	 */
	public function getViewCount(): ?int
	{
		return $this->viewCount;
	}
	
	/**
	 * @return null|int
	 */
	public function getWeeklyViewCount(): ?int
	{
		return $this->weeklyViewCount;
	}
	
	/**
	 * @return int
	 */
	public function getMonthlyViewCount(): int
	{
		return $this->monthlyViewCount;
	}
	
	/**
	 * Increment all view counters.
	 * @return void
	 */
	public function incrementViewCounters(): void
	{
		$this->weeklyViewCount++;
		$this->viewCount++;
		$this->monthlyViewCount++;
	}
	
	/**
	 * @return void
	 */
	public function resetWeeklyViewCount(): void
	{
		$this->weeklyViewCount = 0;
	}
	
	/**
	 * @return void
	 */
	public function resetMonthlyViewCount(): void
	{
		$this->monthlyViewCount = 0;
	}
}