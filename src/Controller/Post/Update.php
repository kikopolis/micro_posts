<?php

declare(strict_types = 1);

namespace App\Controller\Post;

use App\Controller\AbstractController;
use App\Controller\Concerns\DisableDoctrineFiltersConcern;
use App\Controller\Concerns\SanitizeConcern;
use App\Controller\Concerns\TagUserConcern;
use App\Entity\User;
use App\Event\Post\MentionedInPostEvent;
use App\Event\Post\UpdateEvent;
use App\Event\TimeStampableUpdatedEvent;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Security\Voter\Contracts\VotablesContract;
use App\Service\Contracts\FlashContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Update
 * @package App\Controller\Post
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
	 * @var PostRepository
	 */
	private PostRepository $postRepository;
	
	/**
	 * Edit constructor.
	 * @param   EntityManagerInterface     $entityManager
	 * @param   EventDispatcherInterface   $eventDispatcher
	 * @param   PostRepository             $postRepository
	 */
	public function __construct(
		EntityManagerInterface $entityManager,
		EventDispatcherInterface $eventDispatcher,
		PostRepository $postRepository
	)
	{
		$this->entityManager   = $entityManager;
		$this->eventDispatcher = $eventDispatcher;
		$this->postRepository  = $postRepository;
	}
	
	/**
	 * @Route(
	 *     "/posts/{id}/edit",
	 *     name="post.edit",
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
		
		$post = $this->postRepository->find($id);
		
		if (! $post) {
			
			$this->addFlash(
				FlashContract::ERROR,
				sprintf(
					'Post with id "%d" not found',
					$id
				)
			);
			
			return $this->redirectToRoute('post.index');
		}
		
		$this->denyAccessUnlessGranted(VotablesContract::EDIT, $post);
		
		$form = $this->createForm(PostType::class, $post);
		
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			
			$this->eventDispatcher->dispatch(
				new UpdateEvent($post)
			);
			
			$this->eventDispatcher->dispatch(
				new TimeStampableUpdatedEvent($post)
			);
			
			$this->eventDispatcher->dispatch(
				new MentionedInPostEvent(
					$this->tagAndGetUsers($post),
					$post
				)
			);
			
			$post->setBody($this->sanitize($post->getBody()));
			
			$this->entityManager->flush();
			
			$this->addFlash(
				FlashContract::SUCCESS,
				'Post edited successfully.'
			);
			
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