<?php

declare(strict_types = 1);

namespace App\Security\Voter\Contracts;

/**
 * Interface VotablesContract
 * @package App\Security\Voter\Contracts
 */
interface VotablesContract
{
	/**
	 * @var string
	 */
	const VIEW = 'VIEW';
	
	/**
	 * @var string
	 */
	const EDIT = 'EDIT';
	
	/**
	 * @var string
	 */
	const CREATE = 'CREATE';
	
	/**
	 * @var string
	 */
	const APPROVE = 'APPROVE';
	
	/**
	 * @var string
	 */
	const UN_APPROVE = 'UN_APPROVE';
	
	/**
	 * @var string
	 */
	const PUBLISH = 'PUBLISH';
	
	/**
	 * @var string
	 */
	const UN_PUBLISH = 'UN_PUBLISH';
	
	/**
	 * @var string
	 */
	const TRASH = 'TRASH';
	
	/**
	 * @var string
	 */
	const RESTORE = 'RESTORE';
	
	/**
	 * @var string
	 */
	const DELETE = 'DELETE';
	
	/**
	 * @var string
	 */
	const MAKE_ADMIN = 'MAKE_ADMIN';
	
	/**
	 * @var string
	 */
	const MARK_READ = 'MARK_READ';
	
	/**
	 * @var string
	 */
	const ADD_PARTICIPANT = 'ADD_PARTICIPANT';
	
}