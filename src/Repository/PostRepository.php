<?php

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
	/**
	 * PostRepository constructor.
	 * @param  ManagerRegistry  $registry
	 */
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Post::class);
	}
	
	/**
	 * @param  string  $term
	 * @return QueryBuilder
	 */
	public function search(string $term): QueryBuilder
	{
		return $this->createQueryBuilder('p')
		            ->join('p.author', 'u')
		            ->where('u.username = :term')
		            ->orWhere('u.fullname = :term')
		            ->orWhere('p.body = :term')
		            ->setParameter('term', $term)
			;
	}
	
	/**
	 * @param  User   $currentUser
	 * @param  array  $followerIds
	 * @return QueryBuilder
	 */
	public function findFollowersPosts(User $currentUser, array $followerIds = []): QueryBuilder
	{
		$qb = $this->getEntityManager()->createQueryBuilder();
		
		return $qb
			->select('p')
			->from('App\Entity\Post', 'p')
			->where($qb->expr()->in('p.author', $followerIds))
			->andWhere($qb->expr()->not('p.author = :author'))
			->setParameter('author', $currentUser)
			->andWhere('p.trashedAt IS NULL')
			->andWhere('p.publishedAt IS NOT NULL')
			->andWhere('p.approvedAt IS NOT NULL')
			->orderBy('p.createdAt', 'DESC')
			;
	}
	
	/**
	 * @param  User  $currentUser
	 * @return QueryBuilder
	 */
	public function allNotOwn(User $currentUser): QueryBuilder
	{
		$qb = $this->getEntityManager()->createQueryBuilder();
		
		return $qb->select('p')
		          ->from('App\Entity\Post', 'p')
		          ->where($qb->expr()->not('p.author = :author'))
		          ->setParameter('author', $currentUser)
		          ->andWhere('p.trashedAt IS NULL')
		          ->andWhere('p.publishedAt IS NOT NULL')
		          ->andWhere('p.approvedAt IS NOT NULL')
		          ->orderBy('p.createdAt', 'DESC')
			;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function all(): QueryBuilder
	{
		$qb = $this->getEntityManager()->createQueryBuilder();
		
		return $qb->select('p')
		          ->from('App\Entity\Post', 'p')
		          ->leftJoin('App\Entity\User', 'u', Join::WITH, 'p.author = u.id')
		          ->leftJoin('App\Entity\Comment', 'c', Join::WITH, 'c.post = p.id')
		          ->andWhere('p.trashedAt IS NULL')
		          ->andWhere('p.publishedAt IS NOT NULL')
		          ->andWhere('p.approvedAt IS NOT NULL')
		          ->orderBy('p.createdAt', 'DESC')
			;
	}
	
	/**
	 * @param  User  $user
	 * @return QueryBuilder
	 */
	public function allFor(User $user): QueryBuilder
	{
		$qb = $this->getEntityManager()->createQueryBuilder();
		
		return $qb
			->select('p')
			->from('App\Entity\Post', 'p')
			->where('p.author = :author')
			->setParameter('author', $user)
			->andWhere('p.trashedAt IS NULL')
			->andWhere('p.publishedAt IS NOT NULL')
			->andWhere('p.approvedAt IS NOT NULL')
			->orderBy('p.createdAt', 'DESC')
			;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function allForMod(): QueryBuilder
	{
		return $this->createQueryBuilder('p')
		            ->orderBy('p.createdAt', 'DESC')
			;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function reported(): QueryBuilder
	{
		return $this->createQueryBuilder('p')
		            ->where('p.reported = true')
		            ->orderBy('p.createdAt', 'ASC')
			;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function unApproved(): QueryBuilder
	{
		return $this->createQueryBuilder('p')
		            ->where('p.approvedAt IS NULL')
		            ->orderBy('p.createdAt', 'ASC')
			;
	}
}
