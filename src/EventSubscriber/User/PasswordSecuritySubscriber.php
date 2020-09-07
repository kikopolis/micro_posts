<?php

declare(strict_types = 1);

namespace App\EventSubscriber\User;

use App\Event\User\PasswordSecurityEvent;
use App\Service\Contracts\MailerContract;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PasswordSecuritySubscriber implements EventSubscriberInterface
{
	/**
	 * @var MailerContract
	 */
	private MailerContract $mailer;
	
	/**
	 * SecurityPasswordSubscriber constructor.
	 * @param  MailerContract  $mailer
	 */
	public function __construct(MailerContract $mailer)
	{
		$this->mailer = $mailer;
	}
	
	/**
	 * @return array|array[]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			PasswordSecurityEvent::class => ['notify', 999],
		];
	}
	
	/**
	 * @param  PasswordSecurityEvent  $event
	 */
	public function notify(PasswordSecurityEvent $event): void
	{
		$this->mailer->passwordSecurity($event->getUser());
	}
}