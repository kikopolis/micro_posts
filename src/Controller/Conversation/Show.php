<?php

declare(strict_types = 1);

namespace App\Controller\Conversation;

use App\Controller\AbstractController;
use App\Entity\Conversation;
use App\Entity\User;
use App\Security\Voter\Contracts\VotablesContract;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Show
 * @package App\Controller\Conversation
 */
class Show extends AbstractController
{
	/**
	 * @Route(
	 *     "/conversations/{id}/show",
	 *     name="conversation.show",
	 *     methods={"GET"},
	 *     requirements={"id"="\d+"}
	 * )
	 * @param  Conversation  $conversation
	 * @return Response
	 */
	public function __invoke(Conversation $conversation): Response
	{
		$this->denyAccessUnlessGranted(User::ROLE_USER);
		
		$this->denyAccessUnlessGranted(VotablesContract::VIEW, $conversation);
		
		return $this->render(
			'conversation/show.html.twig',
			[
				'conversation' => $conversation,
			]
		);
	}
}