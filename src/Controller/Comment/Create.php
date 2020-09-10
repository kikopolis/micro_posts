<?php

declare(strict_types = 1);

namespace App\Controller\Comment;

use App\Controller\AbstractController;
use App\Controller\Concerns\SanitizeConcern;
use App\Controller\Concerns\TagUserConcern;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Event\AuthorableCreatedEvent;
use App\Event\Comment\CreateEvent;
use App\Event\Comment\FollowedUserCommentsEvent;
use App\Event\Comment\MentionedInCommentEvent;
use App\Event\TimeStampableCreatedEvent;
use App\Form\CommentType;
use App\Security\Voter\Contracts\VotablesContract;
use App\Service\Contracts\FlashContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Create
 * @package App\Controller\Comment
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
	 *     "/posts/{id}/comments",
	 *     name="comment.create",
	 *     methods={"GET", "POST"},
	 *     requirements={"id"="\d+"}
	 * )
	 * @param   Post      $post
	 * @param   Request   $request
	 * @return Response
	 */
	public function __invoke(Post $post, Request $request): Response
	{
		$this->denyAccessUnlessGranted(User::ROLE_USER);
		
		$comment = new Comment();
		
		$this->denyAccessUnlessGranted(VotablesContract::CREATE, $comment);
		
		$form = $this->createForm(CommentType::class, $comment);
		
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			
			/** @var User $user */
			$user = $this->getUser();
			
			$comment->setPost($post);
			
			$this->eventDispatcher->dispatch(
				new AuthorableCreatedEvent(
					$comment, $user
				)
			);
			
			$this->eventDispatcher->dispatch(
				new CreateEvent($comment)
			);
			
			$this->eventDispatcher->dispatch(
				new TimeStampableCreatedEvent($comment)
			);
			
			// Persist the comment to make sure it has an id
			$this->entityManager->persist($comment);
			$this->entityManager->flush();
			
			$this->eventDispatcher->dispatch(
				new FollowedUserCommentsEvent(
					$user->getFollowers()->toArray(),
					$comment
				)
			);
			
			$this->eventDispatcher->dispatch(
				new MentionedInCommentEvent(
					$this->tagAndGetUsers($comment),
					$comment
				)
			);
			
			$this->eventDispatcher->dispatch(
				new CreateEvent($comment)
			);
			
			$comment->setBody($this->sanitize($comment->getBody()));
			
			// Flush again to save notifications and modified body.
			$this->entityManager->flush();
			
			$this->addFlash(
				FlashContract::SUCCESS,
				'Comment added! Before the public can view this comment, a moderator must approve it!'
			);
			
			return $this->redirectToRoute(
				'comment.preview',
				[
					'id' => $comment->getId(),
				]
			);
		}
		
		return $this->render(
			'comment/create.html.twig',
			[
				'form' => $form->createView(),
				'post' => $post,
			]
		);
	}
}