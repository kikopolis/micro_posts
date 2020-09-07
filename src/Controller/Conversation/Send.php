<?php

declare(strict_types = 1);

namespace App\Controller\Conversation;

use App\Controller\AbstractController;
use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Event\AuthorableCreatedEvent;
use App\Event\Message\CreateEvent;
use App\Event\TimeStampableCreatedEvent;
use App\Form\MessageType;
use App\Security\Voter\Contracts\VotablesContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class Send
 * @package App\Controller\Conversation
 */
class Send extends AbstractController
{
	/**
	 * @var EntityManagerInterface
	 */
	private EntityManagerInterface $entityManager;
	
	/**
	 * @var EventDispatcherInterface
	 */
	private EventDispatcherInterface $eventDispatcher;
	
	/**
	 * Send constructor.
	 * @param  EntityManagerInterface    $entityManager
	 * @param  EventDispatcherInterface  $eventDispatcher
	 */
	public function __construct(
		EntityManagerInterface $entityManager,
		EventDispatcherInterface $eventDispatcher
	)
	{
		$this->entityManager   = $entityManager;
		$this->eventDispatcher = $eventDispatcher;
	}
	
	/**
	 * @Route(
	 *     "/conversations/{id}/message",
	 *     name="conversations.new.message",
	 *     methods={"GET", "POST"},
	 *     requirements={"id"="\d+"}
	 * )
	 * @param  Conversation  $conversation
	 * @param  Request       $request
	 * @return Response
	 */
	public function __invoke(
		Conversation $conversation,
		Request $request
	): Response
	{
		$this->denyAccessUnlessGranted(User::ROLE_USER);
		
		$this->denyAccessUnlessGranted(VotablesContract::VIEW, $conversation);
		
		$message = new Message();
		
		$this->denyAccessUnlessGranted(VotablesContract::CREATE, $message);
		
		$form = $this->createForm(MessageType::class, $message);
		
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			
			$message->setConversation($conversation);
			
			$this->eventDispatcher->dispatch(
				new TimeStampableCreatedEvent($message)
			);
			
			$this->eventDispatcher->dispatch(
				new AuthorableCreatedEvent(
					$message,
					$this->getUser()
				)
			);
			
			// Flush to get messages id for the create event notifications.
			$this->entityManager->persist($message);
			$this->entityManager->flush();
			
			$this->eventDispatcher->dispatch(
				new CreateEvent($message)
			);
			
			$this->entityManager->flush();
			
			return $this->redirectToRoute(
				'conversation.show',
				['id' => $conversation->getId()]
			);
		}
		
		return $this->render(
			'base/pure-form.html.twig',
			[
				'form' => $form->createView(),
			]
		);
	}
}