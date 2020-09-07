<?php

declare(strict_types = 1);

namespace App\Event;

use App\Entity\Contracts\TimeStampableContract;
use Symfony\Contracts\EventDispatcher\Event;

class TimeStampableCreatedEvent extends Event
{
	public const NAME = 'timestampable.created';
	
	/**
	 * @var TimeStampableContract
	 */
	private TimeStampableContract $timeStampable;
	
	/**
	 * TimeStampableCreatedEvent constructor.
	 * @param  TimeStampableContract  $timeStampable
	 */
	public function __construct(TimeStampableContract $timeStampable)
	{
		$this->timeStampable = $timeStampable;
	}
	
	/**
	 * @return TimeStampableContract
	 */
	public function getTimeStampable(): TimeStampableContract
	{
		return $this->timeStampable;
	}
}