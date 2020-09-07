<?php

declare(strict_types = 1);

use Codeception\Util\Fixtures;
use DG\BypassFinals;

BypassFinals::enable();

Fixtures::add(
	'testUser',
	[
		'username'    => 'testUser',
		'email'       => 'testuser@test.com',
		'fullName'    => 'Test User',
		'password'    => 'secret',
		'newPassword' => 'SUperSECret3321###',
	]
);

Fixtures::add(
	'activeTestUser',
	[
		'username'    => 'activeTestUser',
		'email'       => 'active-testuser@test.com',
		'fullName'    => 'Test User',
		'password'    => 'secret',
		'newPassword' => 'SUperSECret3321###',
	]
);