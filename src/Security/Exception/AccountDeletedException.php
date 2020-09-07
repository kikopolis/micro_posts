<?php

namespace App\Security\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * @codeCoverageIgnore
 * Class AccountDeletedException
 * @package App\Security\Exception
 */
class AccountDeletedException extends AccountStatusException
{
	/**
	 * @return string
	 */
	public function getMessageKey(): string
	{
		return 'Account has been deleted. Please check your mailbox for a contact
		email regarding this issue and if the email is not there, contact an admin.';
	}
}