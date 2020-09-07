<?php

declare(strict_types = 1);

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class BeforeRequestListener
 * @package App\EventListener
 */
class BeforeRequestListener
{
	/**
	 * @var EntityManagerInterface
	 */
	private EntityManagerInterface $entityManager;
	
	/**
	 * @var Security
	 */
	private Security $security;
	
	/**
	 * BeforeRequestListener constructor.
	 * @param  EntityManagerInterface  $entityManager
	 * @param  Security                $security
	 */
	public function __construct(
		EntityManagerInterface $entityManager,
		Security $security
	)
	{
		$this->entityManager = $entityManager;
		$this->security      = $security;
	}
	
	public function onKernelRequest(): void
	{
		/** @var User $user */
		$user = $this->security->getUser();
		
		if ($user instanceof User
			&& $user->hasRole(User::ROLE_MODERATOR)) {
			
			return;
		}
		
		$this->entityManager
			->getFilters()
			->enable('approvable_filter')
		;
		
		$this->entityManager
			->getFilters()
			->enable('publishable_filter')
		;
		
		$this->entityManager
			->getFilters()
			->enable('trashable_filter')
		;
	}
}