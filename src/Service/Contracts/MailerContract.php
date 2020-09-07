<?php

declare(strict_types = 1);

namespace App\Service\Contracts;

interface MailerContract
{
	/**
	 * @param  string  $to
	 * @param  string  $subject
	 * @param  string  $template
	 * @param  array   $variables
	 */
	public function sendTwigEmail(string $to, string $subject, string $template, array $variables): void;
}