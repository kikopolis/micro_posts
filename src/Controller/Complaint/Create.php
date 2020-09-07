<?php

declare(strict_types = 1);

namespace App\Controller\Complaint;

use App\Controller\AbstractController;
use App\Entity\Complaint;
use App\Entity\User;
use App\Event\AuthorableCreatedEvent;
use App\Event\Complaint\CreateEvent;
use App\Event\TimeStampableCreatedEvent;
use App\Form\ComplaintType;
use App\Repository\UserRepository;
use App\Security\Voter\Contracts\VotablesContract;
use App\Service\Contracts\FlashContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class Create
 * @package App\Controller\Complaint
 */
class Create extends AbstractController
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
	 * @var UserRepository
	 */
	private UserRepository $userRepository;
	
	/**
	 * Create constructor.
	 * @param  EntityManagerInterface    $entityManager
	 * @param  EventDispatcherInterface  $eventDispatcher
	 * @param  UserRepository            $userRepository
	 */
	public function __construct(
		EntityManagerInterface $entityManager,
		EventDispatcherInterface $eventDispatcher,
		UserRepository $userRepository
	)
	{
		$this->entityManager   = $entityManager;
		$this->eventDispatcher = $eventDispatcher;
		$this->userRepository  = $userRepository;
	}
	
	/**
	 * @Route(
	 *     "/complaints/create",
	 *     name="complaint.create",
	 *     methods={"GET", "POST"}
	 * )
	 * @param  Request  $request
	 * @return Response
	 */
	public function __invoke(Request $request): Response
	{
		$complaint = new Complaint();
		
		$this->denyAccessUnlessGranted(
			VotablesContract::CREATE,
			$complaint
		);
		
		$form = $this->createForm(ComplaintType::class, $complaint);
		
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			
			$admins = $this->userRepository->findBy(
				['roles' => User::ROLE_ADMINISTRATOR]
			);
			
			$this->eventDispatcher->dispatch(
				new AuthorableCreatedEvent(
					$complaint,
					$this->getUser()
				)
			);
			
			$this->eventDispatcher->dispatch(
				new TimeStampableCreatedEvent($complaint)
			);
			
			$this->eventDispatcher->dispatch(
				new CreateEvent(
					$complaint,
					$admins
				)
			);
			
			$this->entityManager->persist($complaint);
			$this->entityManager->flush();
			
			$this->addFlash(
				FlashContract::SUCCESS,
				'Your complaint was received. An admin will review it shortly.'
			);
			
			return $this->redirectToRoute('homepage');
		}
		
		return $this->render(
			'base/pure-form.html.twig',
			[
				'form' => $form->createView(),
			]
		);
	}
}