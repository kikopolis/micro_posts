<?php

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
	/**
	 * CommentRepository constructor.
	 * @param  ManagerRegistry  $registry
	 */
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Comment::class);
	}
	
	/**
	 * @param  string  $term
	 * @return QueryBuilder
	 */
	public function search(string $term): QueryBuilder
	{
		return $this->createQueryBuilder('c')
		            ->join('c.author', 'u')
		            ->where('u.username = :term')
		            ->orWhere('u.fullname = :term')
		            ->orWhere('c.body = :term')
		            ->setParameter('term', $term)
			;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function allForMod(): QueryBuilder
	{
		return $this->createQueryBuilder('c')
		            ->orderBy('c.createdAt', 'DESC')
			;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function reported(): QueryBuilder
	{
		return $this->createQueryBuilder('c')
		            ->where('c.reported = true')
		            ->orderBy('c.createdAt', 'ASC')
			;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function unApproved(): QueryBuilder
	{
		return $this->createQueryBuilder('c')
		            ->where('c.approvedAt IS NULL')
		            ->orderBy('c.createdAt', 'ASC')
			;
	}
}
