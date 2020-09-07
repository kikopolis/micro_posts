<?php

declare(strict_types = 1);

namespace App\Controller\Concerns;

/**
 * Class DisableDoctrineFilters
 * @package App\Controller\Concerns
 */
trait DisableDoctrineFiltersConcern
{
	protected function disableAllDoctrineFilters(): void
	{
		$this->disableMany(['approvable_filter', 'publishable_filter', 'trashable_filter']);
	}
	
	protected function disable(string $filter): void
	{
		$this->entityManager->getFilters()->disable($filter);
	}
	
	protected function disableMany(array $filters): void
	{
		foreach ($filters as $filter) {
			
			$this->disable($filter);
		}
	}
}