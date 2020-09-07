<?php

declare(strict_types = 1);

namespace App\Service\DoctrineFilter;

use App\Entity\Contracts\PublishableContract;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

/**
 * Class PublishableFilter
 * @package App\Service\DoctrineFilter
 */
class PublishableFilter extends SQLFilter
{
	/**
	 * @param  ClassMetadata  $targetEntity
	 * @param  string         $targetTableAlias
	 * @return string
	 */
	public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
	{
		if (! $targetEntity->reflClass->implementsInterface(PublishableContract::class)) {
			
			return '';
		}
		
		return sprintf('%s.published_at IS NOT NULL', $targetTableAlias);
	}
	
}