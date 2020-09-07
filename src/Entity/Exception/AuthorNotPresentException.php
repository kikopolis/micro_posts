<?php

declare(strict_types = 1);

namespace App\Entity\Exception;

use Exception;
use Throwable;

/**
 * If an AuthorableContract entity does not have an author set but is needed, throw this.
 * Class AuthorNotPresentException
 * @package App\Entity\Exception
 */
class AuthorNotPresentException extends Exception
{
	/**
	 * AuthorNotPresentException constructor.
	 * @param  string          $message
	 * @param  int             $code
	 * @param  null|Throwable  $previous
	 * @param  string          $entity
	 */
	public function __construct($message = "", $code = 0, Throwable $previous = null, string $entity)
	{
		parent::__construct($message, $code, $previous);
	}
	
	/**
	 * @return string
	 */
	public function getMessageKey(): string
	{
		return sprintf('Author is not set on entity "%s"', $this->entity);
	}
}