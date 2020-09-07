<?php

declare(strict_types = 1);

namespace App\Service\Contracts;

interface FlashContract
{
	public const ERROR   = 'ERROR';
	
	public const WARNING = 'WARNING';
	
	public const INFO    = 'INFO';
	
	public const SUCCESS = 'SUCCESS';
}