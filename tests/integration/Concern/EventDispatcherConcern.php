<?php

declare(strict_types = 1);

namespace App\Tests\integration\Concern;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Trait EventDispatcherConcern
 * @package App\Tests\integration\Concern
 */
trait EventDispatcherConcern
{
	/**
	 * @return EventDispatcherInterface
	 */
	protected function getEd(): EventDispatcherInterface
	{
		return $this->tester->grabService('event_dispatcher');
	}
}