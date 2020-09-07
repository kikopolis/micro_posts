<?php

declare(strict_types = 1);

namespace App\Security\Voter;

use App\Entity\User;
use App\Security\Voter\Concerns\CheckPermissionConcern;
use App\Security\Voter\Contracts\VotablesContract;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class UserVoter
 * @package App\Security\Voter
 */
class UserVoter extends Voter implements VotablesContract
{
	use CheckPermissionConcern;
	
	/**
	 * Determines if the attribute and subject are supported by this voter.
	 * @param  string  $attribute  An attribute
	 * @param  mixed   $subject    The subject to secure, e.g. an object the user wants to access or any other PHP type
	 * @return bool True if the attribute and subject are supported, false otherwise
	 */
	public function supports(string $attribute, $subject): bool
	{
		return in_array(
				$attribute,
				[
					self::VIEW,
					self::EDIT,
					self::CREATE,
					self::DELETE,
					self::TRASH,
					self::RESTORE,
					self::MAKE_ADMIN,
				]
			)
			&& $subject instanceof User;
	}
	
	/**
	 * Perform a single access check operation on a given attribute, subject and token.
	 * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
	 * @param  string          $attribute
	 * @param  mixed           $subject
	 * @param  TokenInterface  $token
	 * @return bool
	 */
	public function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
	{
		if ($this->isAdmin($token) && $attribute !== self::CREATE) {
			
			return true;
		}
		
		switch ($attribute) {
			case self::CREATE:
				return ! $this->hasUser($token);
			case self::VIEW:
			case self::DELETE:
			case self::EDIT:
			case self::TRASH:
			case self::RESTORE:
				return $this->isOwner($subject, $token);
			case self::MAKE_ADMIN:
			default:
				return false;
		}
	}
}