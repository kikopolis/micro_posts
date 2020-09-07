<?php

declare(strict_types = 1);

namespace App\Tests\unit\EventListener;

use App\Entity\User;
use App\EventListener\BeforeRequestListener;
use Codeception\Stub;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Doctrine\ORM\Query\FilterCollection;
use Exception;
use Symfony\Component\Security\Core\Security;

/**
 * @covers  \App\EventListener\BeforeRequestListener
 * Class BeforeRequestListenerTest
 * @package App\Tests\unit\EventListener
 */
class BeforeRequestListenerTest extends Unit
{
	/**
	 * @throws Exception
	 */
	public function testOnKernelRequestWithUser()
	{
		/** @var User $user */
		$user = Stub::make(
			User::class,
			[
				'hasRole' => Stub\Expected::once(false),
			],
			$this
		);
		
		/** @var Security $security */
		$security = Stub::make(
			Security::class,
			[
				'getUser' => Stub\Expected::once($user),
			],
			$this
		);
		
		/** @var SQLFilter $filter */
		$filter = Stub::makeEmpty(
			SQLFilter::class,
			[
			
			],
			$this
		);
		
		/** @var FilterCollection $filterCollection */
		$filterCollection = Stub::make(
			FilterCollection::class,
			[
				'enable' => Stub\Expected::exactly(
					3,
					$filter
				),
			],
			$this
		);
		
		/** @var EntityManagerInterface $em */
		$em = Stub::makeEmpty(
			EntityManagerInterface::class,
			[
				'getFilters' => Stub\Expected::exactly(
					3,
					$filterCollection
				),
			],
			$this
		);
		
		$listener = new BeforeRequestListener(
			$em,
			$security
		);
		
		$listener->onKernelRequest();
	}
	
	/**
	 * @throws Exception
	 */
	public function testOnKernelRequestWithModerator()
	{
		/** @var User $user */
		$user = Stub::make(
			User::class,
			[
				'hasRole' => Stub\Expected::once(true),
			],
			$this
		);
		
		/** @var Security $security */
		$security = Stub::make(
			Security::class,
			[
				'getUser' => Stub\Expected::once($user),
			],
			$this
		);
		
		/** @var SQLFilter $filter */
		$filter = Stub::makeEmpty(
			SQLFilter::class,
			[
			
			],
			$this
		);
		
		/** @var FilterCollection $filterCollection */
		$filterCollection = Stub::make(
			FilterCollection::class,
			[
				'enable' => Stub\Expected::never(),
			],
			$this
		);
		
		/** @var EntityManagerInterface $em */
		$em = Stub::makeEmpty(
			EntityManagerInterface::class,
			[
				'getFilters' => Stub\Expected::never(),
			],
			$this
		);
		
		$listener = new BeforeRequestListener(
			$em,
			$security
		);
		
		$listener->onKernelRequest();
	}
}