<?php

declare(strict_types = 1);

namespace App\Security;

use App\Entity\User;
use App\Security\Exception\AccountDeletedException;
use App\Security\Exception\AccountDisabledException;
use App\Security\Exception\AccountNotActiveException;
use App\Security\Exception\ForcedPasswordChangeException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserChecker
 * @package App\Security
 */
class UserChecker implements UserCheckerInterface
{
	/**
	 * @param  UserInterface  $user
	 * @throws AccountDeletedException|AccountNotActiveException
	 */
	public function checkPreAuth(UserInterface $user): void
	{
		if (! $user instanceof User) {
			
			return;
		}
		
		// If user is deleted, forbid login.
		// Can be deleted by admin (different as disabled) or the user themselves.
		if ($user->isTrashed()) {
			
			throw new AccountDeletedException();
		}
		
		// User must first activate the account to be able to login.
		if (! $user->isActivated()) {
			
			throw new AccountNotActiveException();
		}
	}
	
	/**
	 * @param  UserInterface  $user
	 * @throws AccountDisabledException|ForcedPasswordChangeException
	 */
	public function checkPostAuth(UserInterface $user): void
	{
		if (! $user instanceof User) {
			
			return;
		}
		
		// This disabled feature is only available to admins.
		// The user can login and browse, but not post new content, comments or posts.
		// Exception handled in subscriber
		if ($user->isDisabled()) {
			
			throw new AccountDisabledException();
		}
		
		// Handle Exception in Subscriber
		if ($user->isForcedPasswordChange()) {
			
			throw new ForcedPasswordChangeException();
		}
	}
	
}