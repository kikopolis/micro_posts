<?php

declare(strict_types = 1);

namespace App\EventSubscriber\User;

use App\Security\Exception\AccountDisabledException;
use App\Service\Contracts\FlashContract;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class DisabledExceptionSubscriber
 * @package App\EventSubscriber\User
 */
class DisabledExceptionSubscriber implements EventSubscriberInterface
{
	/**
	 * @var SessionInterface
	 */
	private SessionInterface $session;
	
	/**
	 * DisabledExceptionSubscriber constructor.
	 * @param  SessionInterface  $session
	 */
	public function __construct(SessionInterface $session)
	{
		$this->session = $session;
	}
	
	/**
	 * @return array|array[]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			KernelEvents::EXCEPTION => ['disabled', 999],
		];
	}
	
	/**
	 * @param  ExceptionEvent  $event
	 */
	public function disabled(ExceptionEvent $event): void
	{
		$throwable = $event->getThrowable();
		
		if (! $throwable instanceof AccountDisabledException) {
			
			return;
		}
		
		$this->session->getFlashBag()->add(
			FlashContract::WARNING,
			'Your account has been disabled. You can login and view, but cannot post new content.'
		)
		;
	}
}