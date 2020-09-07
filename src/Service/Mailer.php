<?php

declare(strict_types = 1);

namespace App\Service;

use App\Entity\User;
use App\Service\Contracts\MailerContract;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class Mailer implements MailerContract
{
	/** @var MailerInterface */
	private MailerInterface $mailer;
	
	/**
	 * Email address used to send out emails.
	 * @var string
	 */
	private string $mailFrom;
	
	/**
	 * Admin contact email address.
	 * @var string
	 */
	private string $mailTo;
	
	/**
	 * MailSender constructor.
	 * @param  string           $mailFrom
	 * @param  string           $mailTo
	 * @param  MailerInterface  $mailer
	 */
	public function __construct(string $mailFrom, string $mailTo, MailerInterface $mailer)
	{
		$this->mailer   = $mailer;
		$this->mailFrom = $mailFrom;
		$this->mailTo   = $mailTo;
	}
	
	/**
	 * @param  string  $to
	 * @param  string  $subject
	 * @param  string  $template
	 * @param  array   $variables
	 * @throws TransportExceptionInterface
	 */
	public function sendTwigEmail(string $to, string $subject, string $template, array $variables): void
	{
		$email = (new TemplatedEmail())
			->from($this->mailFrom)
			->to($to)
			->subject($subject)
			->htmlTemplate($template)
			->context($variables)
		;
		
		$this->mailer->send($email);
	}
	
	/**
	 * @param  User  $user
	 * @throws TransportExceptionInterface
	 */
	public function passwordToken(User $user): void
	{
		$this->sendTwigEmail(
			$user->getEmail(),
			'Password reset request for MicroPost',
			'email-templates/new-code-password-reset.html.twig',
			['user' => $user]
		);
	}
	
	/**
	 * @param  User  $user
	 * @throws TransportExceptionInterface
	 */
	public function forcedPasswordToken(User $user): void
	{
		$this->sendTwigEmail(
			$user->getEmail(),
			'Please change your Kikopolis Social password',
			'email-templates/forced-password-reset.html.twig',
			['user' => $user]
		);
	}
	
	/**
	 * @param  User  $user
	 */
	public function activationInfo(User $user): void
	{
	}
	
	/**
	 * @param  User  $user
	 * @throws TransportExceptionInterface
	 */
	public function activationToken(User $user): void
	{
		$this->sendTwigEmail(
			$user->getEmail(),
			'New account activation code request for MicroPost App.',
			'email-templates/new-code-activation.html.twig',
			['user' => $user]
		);
	}
	
	/**
	 * @param  User  $user
	 */
	public function activationSuccess(User $user): void
	{
	}
	
	public function passwordSecurity(User $user): void
	{
	
	}
	
	public function emailSecurity(User $user): void
	{
	
	}
}