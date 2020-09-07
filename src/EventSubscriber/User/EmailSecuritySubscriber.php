<?php

declare(strict_types = 1);

namespace App\EventSubscriber\User;

use App\Event\User\EmailSecurityEvent;
use App\Service\Contracts\MailerContract;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EmailSecuritySubscriber implements EventSubscriberInterface
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
	
	public static function getSubscribedEvents(): array
	{
		return [
			EmailSecurityEvent::class => ['notify', 999],
		];
	}
	
	public function notify(EmailSecurityEvent $event): void
	{
		$this->mailer->emailSecurity($event->getUser());
	}
}