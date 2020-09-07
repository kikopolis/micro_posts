<?php

declare(strict_types = 1);

namespace App\Controller\Post;

use App\Controller\AbstractController;
use App\Controller\Concerns\SanitizeConcern;
use App\Controller\Concerns\TagUserConcern;
use App\Entity\Post;
use App\Entity\User;
use App\Event\AuthorableCreatedEvent;
use App\Event\Post\FollowedUserPostsEvent;
use App\Event\Post\MentionedInPostEvent;
use App\Event\Post\CreateEvent;
use App\Event\TimeStampableCreatedEvent;
use App\Form\PostType;
use App\Security\Voter\Contracts\VotablesContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Create
 * @package App\Controller\Post
 */
class Create extends AbstractController
{
	use TagUserConcern;
	use SanitizeConcern;
	
	/**
	 * @var EntityManagerInterface
	 */
	private EntityManagerInterface $entityManager;
	
	/**
	 * @var EventDispatcherInterface
	 */
	private EventDispatcherInterface $eventDispatcher;
	
	/**
	 * Create constructor.
	 * @param   EntityManagerInterface     $entityManager
	 * @param   EventDispatcherInterface   $eventDispatcher
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
	 *     "/posts/create",
	 *     name="post.create",
	 *     methods={"GET", "POST"}
	 * )
	 * @param   Request   $request
	 * @return Response
	 */
	public function __invoke(Request $request): Response
	{
		$this->denyAccessUnlessGranted(User::ROLE_USER);
		
		$post = new Post();
		
		$this->denyAccessUnlessGranted(VotablesContract::CREATE, $post);
		
		$form = $this->createForm(PostType::class, $post);
		
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			
			/** @var User $user */
			$user = $this->getUser();
			
			$this->eventDispatcher->dispatch(
				new AuthorableCreatedEvent(
					$post, $user
				)
			);
			
			$this->eventDispatcher->dispatch(
				new CreateEvent($post)
			);
			
			$this->eventDispatcher->dispatch(
				new TimeStampableCreatedEvent($post)
			);
			
			$this->entityManager->persist($post);
			//			$this->entityManager->flush();
			
			$this->eventDispatcher->dispatch(
				new FollowedUserPostsEvent(
					$user->getFollowers()->toArray(),
					$post,
					$post->getAuthor()
				)
			);
			
			$this->eventDispatcher->dispatch(
				new MentionedInPostEvent(
					$this->tagAndGetUsers($post),
					$post
				)
			);
			
			$post->setBody($this->sanitize($post->getBody()));
			
			// Flush again for notifications and modified body.
			// All notifications are persisted in local methods, but to avoid a huge
			// amount of write queries, flush once here.
			$this->entityManager->flush();
			
			return $this->redirectToRoute(
				'post.preview',
				[
					'id' => $post->getId(),
				]
			);
		}
		
		return $this->render(
			'post/create.html.twig',
			[
				'form' => $form->createView(),
			]
		);
	}
}