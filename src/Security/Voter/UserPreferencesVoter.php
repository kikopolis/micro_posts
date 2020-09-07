<?php

declare(strict_types = 1);

namespace App\Security\Voter;

use App\Entity\UserPreferences;
use App\Security\Voter\Concerns\CheckPermissionConcern;
use App\Security\Voter\Contracts\VotablesContract;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class UserPreferencesVoter
 * @package App\Security\Voter
 */
class UserPreferencesVoter extends Voter implements VotablesContract
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
					self::CREATE,
					self::VIEW,
					self::EDIT,
					self::DELETE,
				]
			)
			&& $subject instanceof UserPreferences;
	}
	
	/**
	 * @param  string           $attribute
	 * @param  UserPreferences  $subject
	 * @param  TokenInterface   $token
	 * @return bool
	 */
	public function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
	{
		if ($this->isAdmin($token)) {
			
			return true;
		}
		
		switch ($attribute) {
			case self::CREATE:
				return ! $this->hasUser($token);
			case self::VIEW:
				return $this->hasUser($token);
			case self::EDIT:
			case self::DELETE:
				return $this->isOwner($subject, $token);
			default:
				return false;
		}
	}
}