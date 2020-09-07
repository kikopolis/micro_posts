<?php

declare(strict_types = 1);

namespace App\Tests\functional\Concerns;

use App\Tests\FunctionalTester;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Trait EntityManagerConcern
 * @package App\Tests\functional\Concerns
 */
trait EntityManagerConcern
{
	/**
	 * @param  FunctionalTester  $I
	 * @return EntityManagerInterface
	 */
	protected function getEm(FunctionalTester $I): EntityManagerInterface
	{
		return $I->grabService('doctrine')->getManager();
	}
	
	protected function getEmWithoutFilters(FunctionalTester $I): EntityManagerInterface
	{
		$em = $this->getEm($I);
		
		$em->getFilters()->disable('publishable_filter');
		$em->getFilters()->disable('approvable_filter');
		$em->getFilters()->disable('trashable_filter');
		
		return $em;
	}
}