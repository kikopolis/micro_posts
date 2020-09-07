<?php

declare(strict_types = 1);

namespace App\EventSubscriber\User;

use App\Event\User\PasswordTokenEvent;
use App\Service\Contracts\MailerContract;
use App\Service\Contracts\TokenGeneratorContract;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PasswordTokenSubscriber implements EventSubscriberInterface
{
	/**
	 * @var TokenGeneratorContract
	 */
	private TokenGeneratorContract $tokenGenerator;
	
	/**
	 * @var MailerContract
	 */
	private MailerContract $mailer;
	
	/**
	 * PasswordTokenSubscriber constructor.
	 * @param  TokenGeneratorContract  $tokenGenerator
	 * @param  MailerContract          $mailer
	 */
	public function __construct(TokenGeneratorContract $tokenGenerator, MailerContract $mailer)
	{
		$this->tokenGenerator = $tokenGenerator;
		$this->mailer         = $mailer;
	}
	
	/**
	 * @return array|array[]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			PasswordTokenEvent::class => [
				['token', 999],
				['tokenEmail', 998],
			],
		];
	}
	
	/**
	 * @param  PasswordTokenEvent  $event
	 */
	public function token(PasswordTokenEvent $event): void
	{
		$event->getUser()->setPasswordResetToken($this->tokenGenerator->generate());
	}
	
	/**
	 * @param  PasswordTokenEvent  $event
	 */
	public function tokenEmail(PasswordTokenEvent $event): void
	{
		$this->mailer->passwordToken($event->getUser());
	}
}