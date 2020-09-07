<?php

declare(strict_types = 1);

namespace App\Service\Contracts;

interface TokenGeneratorContract
{
	/**
	 * @param  int  $length
	 * @return string
	 */
	public function generate(int $length = 64): string;
}