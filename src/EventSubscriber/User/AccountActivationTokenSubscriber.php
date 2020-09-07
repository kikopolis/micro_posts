<?php

declare(strict_types = 1);

namespace App\EventSubscriber\User;

use App\Event\User\AccountActivationTokenEvent;
use App\Service\Contracts\MailerContract;
use App\Service\Contracts\TokenGeneratorContract;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AccountActivationTokenSubscriber implements EventSubscriberInterface
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
	 * ActivationTokenEventSubscriber constructor.
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
			AccountActivationTokenEvent::class => [
				['token', 999],
				['sendToken', 999],
			],
		];
	}
	
	/**
	 * @param  AccountActivationTokenEvent  $event
	 */
	public function token(AccountActivationTokenEvent $event): void
	{
		$event->getUser()->setAccountActivationToken($this->tokenGenerator->generate());
	}
	
	/**
	 * @param  AccountActivationTokenEvent  $event
	 */
	public function sendToken(AccountActivationTokenEvent $event): void
	{
		$this->mailer->activationToken($event->getUser());
	}
}