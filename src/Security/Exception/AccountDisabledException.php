<?php

namespace App\Security\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * @codeCoverageIgnore
 * Class AccountDisabledException
 * @package App\Security\Exception
 */
class AccountDisabledException extends AccountStatusException
{
	/**
	 * @return string
	 */
	public function getMessageKey(): string
	{
		return 'Your account is disabled. You may login but cannot post new content.';
	}
}