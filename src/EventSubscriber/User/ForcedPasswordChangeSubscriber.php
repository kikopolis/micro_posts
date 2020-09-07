<?php

declare(strict_types = 1);

namespace App\EventSubscriber\User;

use App\Event\User\ForcedPasswordChangeEvent;
use App\Service\Contracts\MailerContract;
use App\Service\Contracts\TokenGeneratorContract;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ForcedPasswordChangeSubscriber
 * @package App\EventSubscriber\User
 */
class ForcedPasswordChangeSubscriber implements EventSubscriberInterface
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
	 * ForcedPasswordChangeSubscriber constructor.
	 * @param  TokenGeneratorContract  $tokenGenerator
	 * @param  MailerContract          $mailer
	 */
	public function __construct(
		TokenGeneratorContract $tokenGenerator,
		MailerContract $mailer
	)
	{
		$this->tokenGenerator = $tokenGenerator;
		$this->mailer         = $mailer;
	}
	
	/**
	 * @return array|\array[][]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			ForcedPasswordChangeEvent::class => [
				['token', 999],
				['sendToken', 998],
			],
		];
	}
	
	/**
	 * @param  ForcedPasswordChangeEvent  $event
	 */
	public function token(ForcedPasswordChangeEvent $event): void
	{
		$event->getUser()->setPasswordResetToken(
			$this->tokenGenerator->generate()
		)
		;
	}
	
	/**
	 * @param  ForcedPasswordChangeEvent  $event
	 */
	public function sendToken(ForcedPasswordChangeEvent $event): void
	{
		$this->mailer->forcedPasswordToken($event->getUser());
	}
}