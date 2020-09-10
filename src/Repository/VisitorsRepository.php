<?php

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Visitor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Visitor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Visitor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Visitor[]    findAll()
 * @method Visitor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VisitorsRepository extends ServiceEntityRepository
{
	/**
	 * VisitorsRepository constructor.
	 * @param  ManagerRegistry  $registry
	 */
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Visitor::class);
	}
}
