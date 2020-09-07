<?php

declare(strict_types = 1);

namespace App\Security\Sanitizer;

use HtmlSanitizer\Node\AbstractTagNode;
use HtmlSanitizer\Node\HasChildrenTrait;

/**
 * @codeCoverageIgnore
 * Class LinkTagNode
 * @package App\Security\Sanitizer
 */
class LinkTagNode extends AbstractTagNode
{
	use HasChildrenTrait;
	
	/**
	 * @return string
	 */
	public function getTagName(): string
	{
		return 'a';
	}
}