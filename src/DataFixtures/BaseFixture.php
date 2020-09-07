<?php

declare(strict_types = 1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

abstract class BaseFixture extends Fixture
{
	/**
	 * @var ObjectManager
	 */
	private ObjectManager $manager;
	
	/**
	 * @var array
	 */
	private array $referencesIndex = [];
	
	/**
	 * @param  ObjectManager  $manager
	 */
	public function load(ObjectManager $manager)
	{
		$this->manager = $manager;
		$this->loadData($manager);
	}
	
	/**
	 * @param  ObjectManager  $manager
	 * @return void
	 */
	abstract protected function loadData(ObjectManager $manager): void;
	
	/**
	 * @param  string    $className
	 * @param  int       $count
	 * @param  callable  $factory
	 */
	protected function createMany(string $className, int $count, callable $factory)
	{
		for ($i = 0; $i < $count; $i++) {
			$entity = new $className();
			$factory($entity, $i);
			$this->manager->persist($entity);
			$this->setReference($className . '_' . $i, $entity);
		}
	}
	
	/**
	 * @param  string  $className
	 * @param  null    $referenceId
	 * @return object
	 * @throws Exception
	 */
	protected function getRandomReference(string $className, $referenceId = null)
	{
		if (! array_key_exists($className, $this->referencesIndex)) {
			$this->referencesIndex[$className] = [];
			foreach ($this->referenceRepository->getReferences() as $key => $ref) {
				if (strpos($key, $className . '_') === 0) {
					$this->referencesIndex[$className][] = $key;
				}
			}
		}
		if (empty($this->referencesIndex[$className])) {
			throw new Exception(sprintf('Cannot find any references for class "%s"', $className));
		}
		if ($referenceId === null) {
			$referenceId = random_int(0, count($this->referencesIndex[$className]) - 1);
		}
		$randomReferenceKey = $this->referencesIndex[$className][$referenceId];
		
		return $this->getReference($randomReferenceKey);
	}
}
