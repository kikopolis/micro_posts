<?php

namespace App\Service;

use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class Statistics
{
	/**
	 * @var bool
	 */
	private bool $statisticsHaveBeenSent = false;
	
	/**
	 * @var PostRepository
	 */
	private PostRepository $postRepository;
	
	/**
	 * @var CommentRepository
	 */
	private CommentRepository $commentRepository;
	
	/**
	 * @var Mailer
	 */
	private Mailer $mailSender;
	
	/**
	 * @var EntityManagerInterface
	 */
	private EntityManagerInterface $entityManager;
	
	/**
	 * @var array
	 */
	private array $weeklyMostViewedPosts = [];
	
	/**
	 * @var array
	 */
	private array $allTimeMostViewedPosts = [];
	
	/**
	 * @var array
	 */
	private array $weeklyTopPosts = [];
	
	/**
	 * @var array
	 */
	private array $allTimeTopPosts = [];
	
	/**
	 * @var array
	 */
	private array $weeklyTopComments = [];
	
	/**
	 * @var array
	 */
	private array $allTimeTopComments = [];
	
	/**
	 * @var string
	 */
	private string $mailTo;
	
	/**
	 * StatisticsService constructor.
	 * @param  PostRepository          $postRepository
	 * @param  CommentRepository       $commentRepository
	 * @param  Mailer                  $mailSender
	 * @param  EntityManagerInterface  $entityManager
	 * @param  string                  $mailTo
	 */
	public function __construct(
		PostRepository $postRepository,
		CommentRepository $commentRepository,
		Mailer $mailSender,
		EntityManagerInterface $entityManager,
		string $mailTo
	)
	{
		$this->postRepository    = $postRepository;
		$this->commentRepository = $commentRepository;
		$this->mailSender        = $mailSender;
		$this->entityManager     = $entityManager;
		$this->mailTo            = $mailTo;
	}
	
	/**
	 * Send the weekly statistics email to the site administrator and handle the reset of weekly statistics.
	 * @throws TransportExceptionInterface
	 */
	public function sendWeeklyStatistics()
	{
		$this->weeklyTopPosts         = $this->postRepository->findBy([], ['weeklyLikeCount' => 'DESC'], 10);
		$this->allTimeTopPosts        = $this->postRepository->findBy([], ['likeCount' => 'DESC'], 10);
		$this->weeklyMostViewedPosts  = $this->postRepository->findBy([], ['weeklyViewCount' => 'DESC'], 10);
		$this->allTimeMostViewedPosts = $this->postRepository->findBy([], ['viewCount' => 'DESC'], 10);
		$this->weeklyTopComments      = $this->commentRepository->findBy([], ['weeklyLikeCount' => 'DESC'], 10);
		$this->allTimeTopComments     = $this->commentRepository->findBy([], ['likeCount' => 'DESC'], 10);
		$this->saveWeeklyStatistics();
		
		$this->mailSender->sendTwigEmail(
			$this->mailTo,
			'Weekly statistics for MicroPost',
			'email-templates/statistics.html.twig',
			[
				'weeklyTopPosts'         => $this->weeklyTopPosts,
				'allTimeTopPosts'        => $this->allTimeTopPosts,
				'weeklyMostViewedPosts'  => $this->weeklyMostViewedPosts,
				'allTimeMostViewedPosts' => $this->allTimeMostViewedPosts,
				'weeklyTopComments'      => $this->weeklyTopComments,
				'allTimeTopComments'     => $this->allTimeTopComments,
			]
		);
		
		$this->statisticsHaveBeenSent = true;
		
		$this->resetWeeklyStatistics();
		
		$this->statisticsHaveBeenSent = false;
	}
	
	/**
	 * Reset the counters for all articles in the database for the weekly statistics.
	 * If $statisticsHaveBeenSent bool is not set to true, do not allow the reset.
	 * Keeping it private to make sure no other class can set it but StatisticsService itself and only should set it
	 * after the statistics have been sent and weekly counts are no longer needed.
	 * @return void
	 */
	private function resetWeeklyStatistics(): void
	{
		if (! $this->statisticsHaveBeenSent) {
			return;
		}
		
		$posts = $this->postRepository->findAll();
		foreach ($posts as $post) {
			$post->resetWeeklyViewCount();
			$post->resetWeeklyLikeCounter();
		}
		
		$comments = $this->commentRepository->findAll();
		foreach ($comments as $comment) {
			$comment->resetWeeklyViewCount();
			$comment->resetWeeklyLikeCounter();
		}
	}
	
	/**
	 * todo write statistics to a file for later retrieval in admin interface.
	 * @return void
	 */
	private function saveWeeklyStatistics(): void
	{
	
	}
}