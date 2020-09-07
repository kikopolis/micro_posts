<?php

declare(strict_types = 1);

namespace App\Event\Post;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class FollowedUserPostsEvent
 * @package App\Event\Post
 */
class FollowedUserPostsEvent extends Event
{
	const NAME = 'followed.user.posts';
	
	/**
	 * @var array|User[]
	 */
	private array $followers;
	
	/**
	 * @var Post
	 */
	private Post $post;
	
	/**
	 * @var User
	 */
	private User $author;
	
	/**
	 * FollowedUserPosts constructor.
	 * @param  array|User[]  $followers
	 * @param  Post          $post
	 * @param  User          $author
	 */
	public function __construct(
		array $followers,
		Post $post,
		User $author
	)
	{
		$this->followers = $followers;
		$this->post      = $post;
		$this->author    = $author;
	}
	
	/**
	 * @return array|User[]
	 */
	public function getFollowers(): array
	{
		return $this->followers;
	}
	
	/**
	 * @return Post
	 */
	public function getPost(): Post
	{
		return $this->post;
	}
	
	/**
	 * @return User
	 */
	public function getAuthor(): User
	{
		return $this->author;
	}
}