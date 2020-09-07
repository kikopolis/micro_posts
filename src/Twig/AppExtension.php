<?php

declare(strict_types = 1);

namespace App\Twig;

use App\Entity\Comment;
use App\Entity\Notification\CommentLikeNotification;
use App\Entity\Notification\CommentPostedNotification;
use App\Entity\Notification\CommentReportedNotification;
use App\Entity\Notification\ComplaintCreatedNotification;
use App\Entity\Notification\FollowedUserCommentsNotification;
use App\Entity\Notification\FollowedUserPostsNotification;
use App\Entity\Notification\FollowNotification;
use App\Entity\Notification\PostLikeNotification;
use App\Entity\Notification\PostReportedNotification;
use App\Entity\Notification\UserMentionedInCommentNotification;
use App\Entity\Notification\UserMentionedInPostNotification;
use App\Entity\Post;
use App\Entity\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigTest;

class AppExtension extends AbstractExtension
{
	public function getTests()
	{
		return [
			new TwigTest(
				'postLikeNotification',
				function ($obj) {
					return $obj instanceof PostLikeNotification;
				}
			),
			new TwigTest(
				'commentLikeNotification',
				function ($obj) {
					return $obj instanceof CommentLikeNotification;
				}
			),
			new TwigTest(
				'commentPostedNotification',
				function ($obj) {
					return $obj instanceof CommentPostedNotification;
				}
			),
			new TwigTest(
				'followNotification',
				function ($obj) {
					return $obj instanceof FollowNotification;
				}
			),
			new TwigTest(
				'followedUserCreatesAPostNotification',
				function ($obj) {
					return $obj instanceof FollowedUserPostsNotification;
				}
			),
			new TwigTest(
				'followedUserCreatesACommentNotification',
				function ($obj) {
					return $obj instanceof FollowedUserCommentsNotification;
				}
			),
			new TwigTest(
				'postReportedNotification',
				function ($obj) {
					return $obj instanceof PostReportedNotification;
				}
			),
			new TwigTest(
				'commentReportedNotification',
				function ($obj) {
					return $obj instanceof CommentReportedNotification;
				}
			),
			new TwigTest(
				'complaintCreatedNotification',
				function ($obj) {
					return $obj instanceof ComplaintCreatedNotification;
				}
			),
			new TwigTest(
				'userMentionedInCommentNotification',
				function ($obj) {
					return $obj instanceof UserMentionedInCommentNotification;
				}
			),
			new TwigTest(
				'userMentionedInPostNotification',
				function ($obj) {
					return $obj instanceof UserMentionedInPostNotification;
				}
			),
			new TwigTest(
				'post',
				function ($obj) {
					return $obj instanceof Post;
				}
			),
			new TwigTest(
				'comment',
				function ($obj) {
					return $obj instanceof Comment;
				}
			),
			new TwigTest(
				'user',
				function ($obj) {
					return $obj instanceof User;
				}
			),
		];
	}
}