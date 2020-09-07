<?php

declare(strict_types = 1);

namespace App\Entity\Contracts;

use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

interface ReportableContract
{
	/**
	 * @param  User  $user
	 * @return ReportableContract
	 */
	public function report(User $user): ReportableContract;
	
	/**
	 * @return bool
	 */
	public function isReported(): bool;
	
	/**
	 * @return int
	 */
	public function getReportCount(): int;
	
	/**
	 * @return ArrayCollection|Collection
	 */
	public function getReportedBy(): Collection;
}