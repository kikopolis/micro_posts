<?php

declare(strict_types = 1);

namespace App\Event\Complaint;

use App\Entity\Complaint;
use Symfony\Contracts\EventDispatcher\Event;

class CreateEvent extends Event
{
	const NAME = 'complaint.create';
	
	/**
	 * @var Complaint
	 */
	private Complaint $complaint;
	
	private array     $admins;
	
	/**
	 * CreateEvent constructor.
	 * @param  Complaint  $complaint
	 * @param  array      $admins
	 */
	public function __construct(Complaint $complaint, array $admins)
	{
		$this->complaint = $complaint;
		$this->admins    = $admins;
	}
	
	/**
	 * @return Complaint
	 */
	public function getComplaint(): Complaint
	{
		return $this->complaint;
	}
	
	/**
	 * @return array
	 */
	public function getAdmins(): array
	{
		return $this->admins;
	}
}