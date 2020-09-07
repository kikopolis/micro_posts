<?php

declare(strict_types = 1);

namespace App\EventSubscriber\User;

use App\Event\User\ActivationEvent;
use App\Service\Contracts\MailerContract;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ActivationSubscriber
 * @package App\EventSubscriber\User
 */
class ActivationSubscriber implements EventSubscriberInterface
{
	/**
	 * @var MailerContract
	 */
	private MailerContract $mailer;
	
	/**
	 * UserActivationSubscriber constructor.
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
			ActivationEvent::class => [
				['activate', 999],
				['successEmail', 998],
			],
		];
	}
	
	/**
	 * @param  ActivationEvent  $event
	 */
	public function activate(ActivationEvent $event): void
	{
		$user = $event->getUser();
		
		$user->activate();
		$user->setAccountActivationToken(null);
	}
	
	/**
	 * @param  ActivationEvent  $event
	 */
	public function successEmail(ActivationEvent $event): void
	{
		$this->mailer->activationSuccess($event->getUser());
	}
}