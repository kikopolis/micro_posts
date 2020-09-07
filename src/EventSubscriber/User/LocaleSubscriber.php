<?php

namespace App\EventSubscriber\User;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
	/**
	 * @var SessionInterface
	 */
	private SessionInterface $session;
	
	/**
	 * UserLocaleSubscriber constructor.
	 * @param  SessionInterface  $session
	 */
	public function __construct(SessionInterface $session)
	{
		$this->session = $session;
	}
	
	/**
	 * @return array
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			SecurityEvents::INTERACTIVE_LOGIN => ['onInteractiveLogin', 10],
		];
	}
	
	/**
	 * @param  InteractiveLoginEvent  $event
	 */
	public function onInteractiveLogin(InteractiveLoginEvent $event): void
	{
		$this->session->set(
			'_locale',
			$event->getAuthenticationToken()
			      ->getUser()
			      ->getPreferences()
			      ->getLocale()
		);
	}
}