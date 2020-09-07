<?php

namespace App\Repository;

use App\Entity\Conversation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Conversation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conversation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conversation[]    findAll()
 * @method Conversation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConversationRepository extends ServiceEntityRepository
{
	/**
	 * ConversationRepository constructor.
	 * @param  ManagerRegistry  $registry
	 */
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Conversation::class);
	}
	
	/**
	 * @param  User  $user
	 * @return QueryBuilder
	 */
	public function forMe(User $user): QueryBuilder
	{
		return $this->createQueryBuilder('c')
		            ->join('c.messages', 'm')
		            ->where('m.conversation = c.id')
		            ->andWhere(':user MEMBER OF c.participants')
		            ->orderBy('m.createdAt', 'DESC')
			;
	}
}
