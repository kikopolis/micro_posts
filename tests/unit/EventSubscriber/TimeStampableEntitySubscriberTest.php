<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventSubscriber;

use App\Entity\Contracts\TimeStampableContract;
use App\Event\TimeStampableCreatedEvent;
use App\Event\TimeStampableUpdatedEvent;
use App\EventSubscriber\TimeStampableEntitySubscriber;
use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;

/**
 * @covers \App\EventSubscriber\TimeStampableEntitySubscriber
 * Class TimeStampableEntitySubscriberTest
 * @package App\Tests\unit\EventSubscriber
 */
class TimeStampableEntitySubscriberTest extends Unit
{
	public function testGetSubscribedEvents()
	{
		$result = TimeStampableEntitySubscriber::getSubscribedEvents();
		
		self::assertArrayHasKey(TimeStampableCreatedEvent::class, $result);
		self::assertArrayHasKey(TimeStampableUpdatedEvent::class, $result);
	}
	
	/**
	 * @throws Exception
	 */
	public function testSetCreationTimeStamp()
	{
		/** @var TimeStampableContract $stampable */
		$stampable = Stub::makeEmpty(
			TimeStampableContract::class,
			[
				'getCreatedAt'          => Stub\Expected::once(null),
				'hasTimestamps'         => Stub\Expected::once(true),
				'setCreationTimeStamps' => Stub\Expected::once(
					Stub::makeEmpty(TimeStampableContract::class)
				),
			],
			$this
		);
		
		/** @var TimeStampableCreatedEvent $event */
		$event = Stub::make(
			TimeStampableCreatedEvent::class,
			[
				'getTimeStampable' => Stub\Expected::once($stampable),
			],
			$this
		);
		
		$subscriber = new TimeStampableEntitySubscriber();
		
		$subscriber->setCreationStamp($event);
	}
	
	/**
	 * @throws Exception
	 */
	public function testSetUpdateStamp()
	{
		/** @var TimeStampableContract $stampable */
		$stampable = Stub::makeEmpty(
			TimeStampableContract::class,
			[
				'getUpdateAt'          => Stub\Expected::once(null),
				'hasTimestamps'        => Stub\Expected::once(true),
				'setUpdatedTimeStamps' => Stub\Expected::once(
					Stub::makeEmpty(TimeStampableContract::class)
				),
			],
			$this
		);
		
		/** @var TimeStampableUpdatedEvent $event */
		$event = Stub::make(
			TimeStampableUpdatedEvent::class,
			[
				'getTimeStampable' => $stampable,
			],
			$this
		);
		
		$subscriber = new TimeStampableEntitySubscriber();
		
		$subscriber->setUpdateStamp($event);
	}
}