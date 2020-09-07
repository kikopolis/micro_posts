<?php

declare(strict_types = 1);

namespace App\Tests\integration\Concern;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Trait EntityManagerConcern
 * @package App\Tests\integration\Concern
 */
trait EntityManagerConcern
{
	/**
	 * @return EntityManagerInterface
	 */
	protected function getEm(): EntityManagerInterface
	{
		return $this->tester->grabService('doctrine')->getManager();
	}
}