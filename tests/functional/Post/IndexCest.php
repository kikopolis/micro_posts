<?php

declare(strict_types = 1);

namespace App\Tests\functional\Post;

use App\Tests\FunctionalTester;

/**
 * @covers \App\Controller\Post\Index
 * Class IndexCest
 * @package App\Tests\functional\Post
 */
class IndexCest
{
	/**
	 * @param  FunctionalTester  $I
	 */
	public function testIndex(FunctionalTester $I)
	{
		$I->amOnPage('/posts');
		
		$I->seeResponseCodeIs(200);
		
		$I->seeInSource('Glorious micro posts for the masses! Get yours while they last!!!');
	}
}