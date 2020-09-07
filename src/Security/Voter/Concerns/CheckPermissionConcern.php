<?php

declare(strict_types = 1);

namespace App\Security\Voter\Concerns;

use App\Entity\Contracts\AuthorableContract;
use App\Entity\Contracts\OwnableContract;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Trait CheckPermissionConcern
 * @package App\Security\Voter\Concerns
 */
trait CheckPermissionConcern
{
	/**
	 * @param  TokenInterface  $token
	 * @return bool
	 */
	public function hasUser(TokenInterface $token): bool
	{
		return $token->getUser() instanceof User;
	}
	
	/**
	 * @param  TokenInterface  $token
	 * @return null|User
	 */
	public function getUser(TokenInterface $token): ?User
	{
		if (! $this->hasUser($token)) {
			
			return null;
		}
		
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $token->getUser();
	}
	
	/**
	 * @param  TokenInterface  $token
	 * @return bool
	 * @noinspection PhpPossiblePolymorphicInvocationInspection
	 */
	public function isModerator(TokenInterface $token): bool
	{
		if (! $this->hasUser($token)) {
			
			return false;
		}
		
		return $token->getUser()->hasRole(User::ROLE_MODERATOR);
	}
	
	/**
	 * @param  TokenInterface  $token
	 * @return bool
	 */
	public function isAdmin(TokenInterface $token): bool
	{
		if (! $this->hasUser($token)) {
			
			return false;
		}
		
		/** @var User $user */
		$user = $token->getUser();
		
		return $user->hasRole(User::ROLE_ADMINISTRATOR)
			|| $user->hasRole(User::ROLE_SUPER_ADMINISTRATOR);
	}
	
	/**
	 * @param  TokenInterface  $token
	 * @return bool
	 */
	public function isClean(TokenInterface $token): bool
	{
		if (! $this->hasUser($token)) {
			
			return false;
		}
		
		/** @var User $user */
		$user = $token->getUser();
		
		return $user->isActivated()
			&& ! $user->isDisabled()
			&& ! $user->isTrashed();
	}
	
	/**
	 * @param  AuthorableContract|User|mixed  $subject
	 * @param  TokenInterface                 $token
	 * @return bool
	 */
	public function isOwner($subject, TokenInterface $token): bool
	{
		if (! $this->hasUser($token)) {
			
			return false;
		}
		
		return $this->check($subject, $token);
	}
	
	/**
	 * @param  AuthorableContract|OwnableContract|mixed  $subject
	 * @param  TokenInterface                            $token
	 * @return bool
	 * @noinspection PhpPossiblePolymorphicInvocationInspection
	 */
	protected function check($subject, TokenInterface $token): bool
	{
		switch ($subject) {
			case $subject instanceof AuthorableContract:
				return $token->getUser()->getId() === $subject->getAuthor()->getId();
			case $subject instanceof OwnableContract:
				return $token->getUser()->getId() === $subject->getOwner()->getId();
			case $subject instanceof User:
				return $token->getUser()->getId() === $subject->getId();
			case method_exists($subject, 'getUser'):
				return $token->getUser()->getId() === $subject->getUser()->getId();
			default:
				return false;
		}
	}
}