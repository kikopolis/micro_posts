<?php

namespace App\Security\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * @codeCoverageIgnore
 * Class ForcedPasswordChangeException
 * @package App\Security\Exception
 */
class ForcedPasswordChangeException extends AccountStatusException
{
	/**
	 * @return string
	 */
	public function getMessageKey(): string
	{
		return 'You must change your password before logging in.';
	}
}