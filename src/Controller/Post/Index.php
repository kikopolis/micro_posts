<?php

declare(strict_types = 1);

namespace App\Controller\Post;

use App\Controller\AbstractController;
use App\Controller\Concerns\HomeWithFlashConcern;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\UserPreferences;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Index
 * @package App\Controller\Post
 */
class Index extends AbstractController
{
	use HomeWithFlashConcern;
	
	/**
	 * @var PostRepository
	 */
	private PostRepository $postRepository;
	
	/**
	 * @var UserRepository
	 */
	private UserRepository $userRepository;
	
	/**
	 * @var PaginatorInterface
	 */
	private PaginatorInterface $paginator;
	
	/**
	 * Index constructor.
	 * @param  PostRepository      $postRepository
	 * @param  UserRepository      $userRepository
	 * @param  PaginatorInterface  $paginator
	 */
	public function __construct(
		PostRepository $postRepository,
		UserRepository $userRepository,
		PaginatorInterface $paginator
	)
	{
		$this->postRepository = $postRepository;
		$this->userRepository = $userRepository;
		$this->paginator      = $paginator;
	}
	
	/**
	 * @Route(
	 *     "/posts/{page}",
	 *     name="post.index",
	 *     methods={"GET"},
	 *     defaults={"page"=1},
	 *     requirements={"page"="\d+"}
	 * )
	 * @param  int      $page
	 * @param  Request  $request
	 * @return Response
	 */
	public function __invoke(Request $request, int $page = 1): Response
	{
		// No need for all the work and variables if we are dealing with an anonymous user.
		// Do not show new users or users to follow for anon.
		if ($this->isGranted('ROLE_USER')) {
			
			/** @var User $user */
			$user = $this->getUser();
			
			$followedUsers = $user->getFollowing()
			                      ->filter(fn(User $user) => $user->getId())
			                      ->toArray()
			;
		}
		
		// For a logged in user that does not follow anyone,
		// Create an array of *heavy* posting users to suggest.
		if (! isset($followedUsers) && isset($user)) {
			
			$usersToFollow = $this->userRepository
				->findLimitedActivePosters($user);
		}
		
		// Get the sorting base the user has specified in preferences.
		// We must also first check for variables to be defined and
		// if the user is following anyone.
		// If any of the conditions is not met, we will use default and sort by newest random posts.
		if (isset($user) && isset($followedUsers) && count($followedUsers) > 0) {
			
			if (
				$user->getPreferences()->getSortHomePageBy()
				=== UserPreferences::SORT_BY_FOLLOWED_USERS_NEWEST_FIRST
			) {
				// If user wishes to sort by followed users for front page.
				$qb = $this->postRepository
					->findFollowersPosts($user, $followedUsers);
			} else {
				
				$qb = $this->postRepository
					->allNotOwn($user);
			}
		} else {
			
			$qb = $this->postRepository
				->all();
		}
		
		$page = (int) $request->get('page') ?? $page;
		
		$limit = $request->get('limit');
		
		$limit = $limit ? (int) $limit : Post::PAGINATION_PER_PAGE;
		
		$posts = $this->paginator->paginate(
			$qb,
			$page,
			$limit
		);
		
		return $this->render(
			'post/index.html.twig',
			[
				'pagination'    => $posts,
				'usersToFollow' => $usersToFollow ?? [],
				'title'         => 'Glorious micro posts for the masses! Get yours while they last!!!',
			]
		);
	}
}