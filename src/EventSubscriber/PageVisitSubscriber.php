<?php

declare(strict_types = 1);

namespace App\EventSubscriber;

use App\Entity\Visitor;
use App\Event\TimeStampableCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class PageVisitSubscriber
 * @package App\EventSubscriber
 * @author  Kristo Leas <kristo.leas@gmail.com>
 */
class PageVisitSubscriber implements EventSubscriberInterface
{
	/**
	 * @var EntityManagerInterface
	 */
	private EntityManagerInterface $entityManager;
	
	/**
	 * @var TokenStorageInterface
	 */
	private TokenStorageInterface $tokenStorage;
	
	/**
	 * @var RouterInterface
	 */
	private RouterInterface $router;
	
	/**
	 * @var EventDispatcherInterface
	 */
	private EventDispatcherInterface $eventDispatcher;
	
	/**
	 * PageVisitSubscriber constructor.
	 * @param   EntityManagerInterface     $entityManager
	 * @param   TokenStorageInterface      $tokenStorage
	 * @param   RouterInterface            $router
	 * @param   EventDispatcherInterface   $eventDispatcher
	 */
	public function __construct(
		EntityManagerInterface $entityManager,
		TokenStorageInterface $tokenStorage,
		RouterInterface $router,
		EventDispatcherInterface $eventDispatcher
	)
	{
		$this->entityManager   = $entityManager;
		$this->tokenStorage    = $tokenStorage;
		$this->router          = $router;
		$this->eventDispatcher = $eventDispatcher;
	}
	
	/**
	 * @return array[]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			KernelEvents::RESPONSE => ['onKernelResponse', 999],
		];
	}
	
	public function onKernelResponse(ResponseEvent $event): void
	{
		// TODO KEEP LIKE THIS FOR DEBUGGING
		$route      = $event->getRequest()->attributes->get('_route') ? $event->getRequest()->attributes->get('_route') : null;
		$controller = $event->getRequest()->attributes->get('_controller');
		
		$visitor = new Visitor();
		
		$visitor->setClientIp($event->getRequest()->getClientIp());
		$visitor->setRoute((string) $route);
		$visitor->setController((string) $controller);
		$visitor->setBrowser($event->getRequest()->headers->get('User-Agent'));
		
		$this->eventDispatcher->dispatch(new TimeStampableCreatedEvent($visitor));
		
		$this->entityManager->persist($visitor);
		$this->entityManager->flush();
	}
}