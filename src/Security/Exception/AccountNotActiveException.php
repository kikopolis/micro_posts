<?php

namespace App\Security\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * @codeCoverageIgnore
 * Class AccountNotActiveException
 * @package App\Security\Exception
 */
class AccountNotActiveException extends AccountStatusException
{
	/**
	 * @return string
	 */
	public function getMessageKey(): string
	{
		return 'Account has not been activated yet. Please check your e-mail for the confirmation!';
	}
}