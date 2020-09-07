<?php

declare(strict_types = 1);

namespace App\Service\DoctrineFilter;

use App\Entity\Contracts\ApprovableConctract;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

/**
 * Class ApprovableFilter
 * @package App\Service\DoctrineFilter
 */
class ApprovableFilter extends SQLFilter
{
	
	
	/**
	 * @param  ClassMetadata  $targetEntity
	 * @param  string         $targetTableAlias
	 * @return string
	 */
	public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
	{
		if (! $targetEntity->reflClass->implementsInterface(ApprovableConctract::class)) {
			
			return '';
		}
		
		return sprintf('%s.approved_at IS NOT NULL', $targetTableAlias);
	}
}