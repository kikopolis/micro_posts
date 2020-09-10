<?php

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
	/**
	 * UserRepository constructor.
	 * @param  ManagerRegistry  $registry
	 */
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, User::class);
	}
	
	/**
	 * @param  string  $term
	 * @return QueryBuilder
	 */
	public function search(string $term): QueryBuilder
	{
		return $this->createQueryBuilder('u')
		            ->where('u.username = :term')
		            ->orWhere('u.fullname = :term')
		            ->setParameter('term', $term)
			;
	}
	
	/**
	 * @param  User  $user
	 * @param  int   $limit
	 * @return int|mixed|string
	 */
	public function findLimitedActivePosters(User $user, int $limit = 15)
	{
		return $this->findAllActivePosters()
		            ->andHaving('u != :user')
		            ->setParameter('user', $user)
		            ->setMaxResults($limit)
		            ->getQuery()
		            ->getResult()
			;
	}
	
	private function findAllActivePosters(): QueryBuilder
	{
		return $this->createQueryBuilder('u')
		            ->select('u')
		            ->where('u.activated = true')
		            ->andWhere('u.trashedAt IS NULL')
		            ->innerJoin('u.posts', 'p')
		            ->groupBy('u')
		            ->having('count(p) >= 5')
		            ->andWhere('p.trashedAt IS NULL')
		            ->andWhere('p.publishedAt IS NOT NULL')
		            ->andWhere('p.approvedAt IS NOT NULL')
			;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function forAdmin(): QueryBuilder
	{
		return $this->createQueryBuilder('u')
		            ->orderBy('u.createdAt', 'DESC')
			;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function trashed(): QueryBuilder
	{
		return $this->createQueryBuilder('u')
		            ->where('u.trashedAt IS NOT NULL')
		            ->orderBy('u.trashedAt', 'DESC')
			;
	}
}
