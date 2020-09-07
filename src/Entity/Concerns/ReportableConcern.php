<?php

declare(strict_types = 1);

namespace App\Entity\Concerns;

use App\Entity\Contracts\ReportableContract;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @property bool       $reported
 * @property Collection $reportedBy
 * @property int        $reportCount
 * Trait ReportableConcern
 * @package App\Entity\Concerns
 */
trait ReportableConcern
{
	/**
	 * @param  User  $user
	 * @return $this|ReportableContract
	 */
	public function report(User $user): self
	{
		if ($this->isReported() === false) {
			
			$this->reported = true;
		}
		
		$this->addReportedBy($user);
		
		$this->reportCount++;
		
		return $this;
	}
	
	/**
	 * Function for mods to make a post kosher again.
	 * @return $this|ReportableContract
	 */
	public function unReport(): self
	{
		$this->reported = false;
		
		$this->reportedBy->clear();
		
		$this->reportCount = 0;
		
		return $this;
	}
	
	/**
	 * @return bool
	 */
	public function isReported(): bool
	{
		return $this->reported;
	}
	
	/**
	 * @return int
	 */
	public function getReportCount(): int
	{
		return $this->reportCount;
	}
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getReportedBy(): Collection
	{
		return $this->reportedBy;
	}
	
	/**
	 * @param  User  $user
	 * @return $this|ReportableContract
	 */
	public function addReportedBy(User $user): self
	{
		if (! $this->reportedBy->contains($user)) {
			
			$this->reportedBy->add($user);
		}
		
		return $this;
	}
}