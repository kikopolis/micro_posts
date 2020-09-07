<?php

declare(strict_types = 1);

namespace App\Event\Complaint;

use App\Entity\Complaint;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class DeleteEvent
 * @package App\Event\Complaint
 */
class DeleteEvent extends Event
{
	const NAME = 'complaint.delete';
	
	/**
	 * @var Complaint
	 */
	private Complaint $complaint;
	
	/**
	 * @var User
	 */
	private User $deletedBy;
	
	/**
	 * DeleteEvent constructor.
	 * @param  Complaint  $complaint
	 * @param  User       $deletedBy
	 */
	public function __construct(Complaint $complaint, User $deletedBy)
	{
		$this->complaint = $complaint;
		$this->deletedBy = $deletedBy;
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
	public function getDeletedBy(): User
	{
		return $this->deletedBy;
	}
}