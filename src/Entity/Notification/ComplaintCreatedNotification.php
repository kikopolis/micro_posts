<?php

declare(strict_types = 1);

namespace App\Entity\Notification;

use App\Entity\Complaint;
use App\Entity\Contracts\NotificationContract;
use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * Class ComplaintCreatedNotification
 * @package App\Entity\Notification
 */
class ComplaintCreatedNotification extends Notification implements NotificationContract
{
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Complaint")
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 */
	private Complaint $complaint;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User")
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 * @var User
	 */
	private User $complainer;
	
	/**
	 * ComplaintCreatedNotification constructor.
	 * @param  User       $owner
	 * @param  Complaint  $complaint
	 * @param  User       $complainer
	 */
	public function __construct(
		User $owner,
		Complaint $complaint,
		User $complainer
	)
	{
		$this->owner      = $owner;
		$this->complaint  = $complaint;
		$this->complainer = $complainer;
		
		$this->setIsModNote(true);
	}
	
	/**
	 * @return Complaint
	 */
	public function getComplaint(): Complaint
	{
		return $this->complaint;
	}
	
	/**
	 * @return User
	 */
	public function getComplainer(): User
	{
		return $this->complainer;
	}
	
	/**
	 * @return string
	 */
	public function getText(): string
	{
		return sprintf(
			'User "%s" has lodged a complaint agains user "%s".',
			$this->getComplainer()->getUsername(), $this->getComplaint()->getTarget()->getUsername()
		);
	}
}