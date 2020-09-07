<?php

declare(strict_types = 1);

namespace App\EventSubscriber\User;

use App\Security\Exception\ForcedPasswordChangeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class ForcedPasswordExceptionSubscriber
 * @package App\EventSubscriber\User
 */
class ForcedPasswordExceptionSubscriber implements EventSubscriberInterface
{
	/**
	 * @var RouterInterface
	 */
	private RouterInterface $router;
	
	/**
	 * ForcedPasswordExceptionSubscriber constructor.
	 * @param  RouterInterface  $router
	 */
	public function __construct(RouterInterface $router)
	{
		$this->router = $router;
	}
	
	/**
	 * @return array|array[]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			KernelEvents::EXCEPTION => ['forcedException', 999],
		];
	}
	
	/**
	 * @param  ExceptionEvent  $event
	 */
	public function forcedException(ExceptionEvent $event): void
	{
		$throwable = $event->getThrowable();
		
		if (! $throwable instanceof ForcedPasswordChangeException) {
			
			return;
		}
		
		$pwdChangeRoute = $this->router->generate('change-password');
		
		$event->setResponse(new RedirectResponse($pwdChangeRoute));
	}
}