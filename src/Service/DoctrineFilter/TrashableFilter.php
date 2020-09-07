<?php

declare(strict_types = 1);

namespace App\Service\DoctrineFilter;

use App\Entity\Contracts\TrashableContract;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

/**
 * Class TrashableFilter
 * @package App\Service\DoctrineFilter
 */
class TrashableFilter extends SQLFilter
{
	/**
	 * @param  ClassMetadata  $targetEntity
	 * @param  string         $targetTableAlias
	 * @return string
	 */
	public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
	{
		if (! $targetEntity->reflClass->implementsInterface(TrashableContract::class)) {
			
			return '';
		}
		
		return sprintf('%s.trashed_at IS NULL', $targetTableAlias);
	}
}