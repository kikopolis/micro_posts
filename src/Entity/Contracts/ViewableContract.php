<?php

declare(strict_types = 1);

namespace App\Entity\Contracts;

interface ViewableContract
{
	/**
	 * @return null|int
	 */
	public function getViewCount(): ?int;
	
	/**
	 * @return null|int
	 */
	public function getWeeklyViewCount(): ?int;
	
	/**
	 * Increment all view counters.
	 * @return void
	 */
	public function incrementViewCounters(): void;
	
	/**
	 * Reset the weekly view counter. Only recommend to have weekly counter reset, otherwise, why bother with all time?
	 * @return void
	 */
	public function resetWeeklyViewCount(): void;
}