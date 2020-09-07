<?php

declare(strict_types = 1);

namespace App\Service;

use App\Service\Contracts\TokenGeneratorContract;
use Throwable;

/**
 * Class TokenGenerator
 * @package App\Service
 */
class TokenGenerator implements TokenGeneratorContract
{
	private const ALHPA_NUMERAL_BET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	
	private const ALPHABET          = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	
	/**
	 * @param  int  $length
	 * @return string
	 */
	public function generate(int $length = 64): string
	{
		$maxNumber = strlen(self::ALHPA_NUMERAL_BET);
		$token     = '';
		
		for ($i = 0; $i < $length; $i++) {
			
			$randomInt = $this->getRandomInt($maxNumber);
			
			$token .= self::ALHPA_NUMERAL_BET[$randomInt];
		}
		
		return $token;
	}
	
	/**
	 * @param  int  $length
	 * @return string
	 */
	public function letters(int $length = 64): string
	{
		$maxNumber = strlen(self::ALPHABET);
		$token     = '';
		
		for ($i = 0; $i < $length; $i++) {
			
			$randomInt = $this->getRandomInt($maxNumber);
			
			$token .= self::ALPHABET[$randomInt];
		}
		
		return $token;
	}
	
	/**
	 * @param $maxNumber
	 * @return int
	 */
	public function getRandomInt($maxNumber): int
	{
		try {
			
			return random_int(0, $maxNumber - 1);
		} catch (Throwable $e) {
			
			return $this->getRandomInt($maxNumber);
		}
	}
}