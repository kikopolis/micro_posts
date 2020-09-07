<?php

declare(strict_types = 1);

namespace App\Security\Sanitizer;

use HtmlSanitizer\Extension\ExtensionInterface;
use HtmlSanitizer\Visitor\NodeVisitorInterface;

/**
 * @codeCoverageIgnore
 * Class LinkTagExtension
 * @package App\Security\Sanitizer
 */
class LinkTagExtension implements ExtensionInterface
{
	/**
	 * @return string
	 */
	public function getName(): string
	{
		return 'a';
	}
	
	/**
	 * @param  array  $config
	 * @return LinkTagNodeVisitor[]
	 */
	public function createNodeVisitors(array $config = []): array
	{
		return [
			'a' => new LinkTagNodeVisitor($config['tags']['a'] ?? []),
		];
	}
}