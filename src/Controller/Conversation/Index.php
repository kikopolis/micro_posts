<?php

declare(strict_types = 1);

namespace App\Controller\Conversation;

use App\Controller\AbstractController;
use App\Entity\User;
use App\Repository\ConversationRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Index
 * @package App\Controller\Conversation
 */
class Index extends AbstractController
{
	/**
	 * @var ConversationRepository
	 */
	private ConversationRepository $conversationRepository;
	
	/**
	 * @var PaginatorInterface
	 */
	private PaginatorInterface $paginator;
	
	/**
	 * Index constructor.
	 * @param  ConversationRepository  $conversationRepository
	 * @param  PaginatorInterface      $paginator
	 */
	public function __construct(
		ConversationRepository $conversationRepository,
		PaginatorInterface $paginator
	)
	{
		$this->conversationRepository = $conversationRepository;
		$this->paginator              = $paginator;
	}
	
	/**
	 * @Route(
	 *     "/conversations/{page}/{limit}",
	 *     name="conversations.index",
	 *     methods={"GET"},
	 *     requirements={"page"="\d+", "limit"="\d+"},
	 *     defaults={"page"=1, "limit"=10}
	 * )
	 * @param  int  $page
	 * @param  int  $limit
	 * @return Response
	 */
	public function __invoke(int $page = 1, int $limit = 10): Response
	{
		$this->denyAccessUnlessGranted(User::ROLE_USER);
		
		$conversations = $this->paginator->paginate(
			$this->conversationRepository->forMe(
				$this->getUser()
			),
			$page,
			$limit
		);
		
		return $this->render(
			'conversation/index.html.twig',
			[
				'conversations' => $conversations,
			]
		);
	}
}