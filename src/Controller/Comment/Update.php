<?php

declare(strict_types = 1);

namespace App\Controller\Comment;

use App\Controller\AbstractController;
use App\Controller\Concerns\DisableDoctrineFiltersConcern;
use App\Controller\Concerns\SanitizeConcern;
use App\Controller\Concerns\TagUserConcern;
use App\Entity\User;
use App\Event\Comment\MentionedInCommentEvent;
use App\Event\Comment\UpdateEvent;
use App\Event\TimeStampableUpdatedEvent;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Security\Voter\Contracts\VotablesContract;
use App\Service\Contracts\FlashContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Update
 * @package App\Controller\Comment
 */
class Update extends AbstractController
{
	use TagUserConcern;
	use DisableDoctrineFiltersConcern;
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
	 * @var CommentRepository
	 */
	private CommentRepository $commentRepository;
	
	/**
	 * Edit constructor.
	 * @param   EntityManagerInterface     $entityManager
	 * @param   EventDispatcherInterface   $eventDispatcher
	 * @param   CommentRepository          $commentRepository
	 */
	public function __construct(
		EntityManagerInterface $entityManager,
		EventDispatcherInterface $eventDispatcher,
		CommentRepository $commentRepository
	)
	{
		$this->entityManager     = $entityManager;
		$this->eventDispatcher   = $eventDispatcher;
		$this->commentRepository = $commentRepository;
	}
	
	/**
	 * @Route(
	 *     "/comments/{id}/edit",
	 *     name="comment.edit",
	 *     methods={"GET", "POST"},
	 *     requirements={"id"="\d+"}
	 * )
	 * @param   int       $id
	 * @param   Request   $request
	 * @return Response
	 */
	public function __invoke(int $id, Request $request): Response
	{
		$this->denyAccessUnlessGranted(User::ROLE_USER);
		
		$this->disableAllDoctrineFilters();
		
		$comment = $this->commentRepository->find($id);
		
		if (! $comment) {
			
			$this->addFlash(
				FlashContract::ERROR,
				sprintf(
					'Comment with id "%d" not found',
					$id
				)
			);
			
			return $this->redirectToRoute('post.index');
		}
		
		$this->denyAccessUnlessGranted(VotablesContract::EDIT, $comment);
		
		$form = $this->createForm(CommentType::class, $comment);
		
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			
			$this->eventDispatcher->dispatch(
				new UpdateEvent($comment)
			);
			
			$this->eventDispatcher->dispatch(
				new TimeStampableUpdatedEvent($comment)
			);
			
			$this->eventDispatcher->dispatch(
				new MentionedInCommentEvent(
					$this->tagAndGetUsers($comment),
					$comment
				)
			);
			
			$comment->setBody($this->sanitize($comment->getBody()));
			
			$this->entityManager->flush();
			
			$this->addFlash(
				FlashContract::SUCCESS,
				'Comment edited successfully'
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
				'post' => $comment->getPost(),
				'form' => $form->createView(),
			]
		);
	}
}