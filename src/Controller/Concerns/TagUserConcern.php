<?php

declare(strict_types = 1);

namespace App\Controller\Concerns;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;

/**
 * Trait TagUserConcern
 * @package App\Controller\Concerns
 */
trait TagUserConcern
{
	/**
	 * Parse all valid @ user names in the text string to a username link and return an array of tagged users.
	 * @param   Post|Comment   $entity
	 * @param   string         $symbol
	 * @return array
	 */
	protected function tagAndGetUsers($entity, string $symbol = '@'): array
	{
		$taggedUsers = [];
		
		preg_match_all("/{$symbol}(?P<username>[\w_\d]+)/", $entity->getBody(), $matches, PREG_SET_ORDER);
		
		if (count($matches) > 0) {
			
			foreach ($matches as $match) {
				
				/** @var User $user */
				$user = $this->checkUserName($match['username']);
				
				if ($user) {
					$taggedUsers[] = $user;
					
					$link = "<a href='/profile/{$match['username']}/show'>{$symbol}{$match['username']}</a>";
					
					$entity->setBody(preg_replace("/{$symbol}{$match['username']}\b/", $link, $entity->getBody()));
				}
			}
		}
		
		return $taggedUsers;
	}
	
	/**
	 * Check if a username exists in the database.
	 * @param   string   $username
	 * @return null|object
	 */
	protected function checkUserName(string $username): ?object
	{
		return $this->getDoctrine()->getRepository('App:User')->findOneBy(['username' => $username]);
	}
}