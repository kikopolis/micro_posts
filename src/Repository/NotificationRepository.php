<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
	/**
	 * NotificationRepository constructor.
	 * @param  ManagerRegistry  $registry
	 */
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Notification::class);
	}
	
	/**
	 * @param  User  $user
	 * @return int|mixed|string
	 */
	public function modNotes(User $user)
	{
		return $this->createQueryBuilder('n')
		            ->where('n.author = :user')
		            ->andWhere('n.seen = false')
		            ->andWhere('n.isModNote = true')
		            ->setParameter('author', $user)
		            ->orderBy('n.createdAt', 'DESC')
		            ->getQuery()
		            ->getResult()
			;
	}
	
	/**
	 * @param  User  $user
	 * @return int|mixed|string
	 */
	public function newNotes(User $user)
	{
		return $this->createQueryBuilder('n')
		            ->where('n.author = :user')
		            ->andWhere('n.seen = false')
		            ->setParameter('author', $user)
		            ->orderBy('n.createdAt', 'DESC')
		            ->getQuery()
		            ->getResult()
			;
	}
	
	/**
	 * @param  User  $user
	 * @return int|mixed|string
	 */
	public function oldNotes(User $user)
	{
		return $this->createQueryBuilder('n')
		            ->where('n.author = :user')
		            ->andWhere('n.seen = true')
		            ->setParameter('author', $user)
		            ->orderBy('n.createdAt', 'DESC')
		            ->getQuery()
		            ->getResult()
			;
	}
}
