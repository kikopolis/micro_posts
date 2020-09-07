<?php

declare(strict_types = 1);

namespace App\Security\Sanitizer;

use DOMNode;
use HtmlSanitizer\Model\Cursor;
use HtmlSanitizer\Node\NodeInterface;
use HtmlSanitizer\Visitor\AbstractNodeVisitor;
use HtmlSanitizer\Visitor\HasChildrenNodeVisitorTrait;
use HtmlSanitizer\Visitor\NamedNodeVisitorInterface;

/**
 * @codeCoverageIgnore
 * Class LinkTagNodeVisitor
 * @package App\Security\Sanitizer
 */
class LinkTagNodeVisitor extends AbstractNodeVisitor implements NamedNodeVisitorInterface
{
	use HasChildrenNodeVisitorTrait;
	
	/**
	 * @param  DOMNode  $domNode
	 * @param  Cursor   $cursor
	 * @return NodeInterface
	 */
	protected function createNode(DOMNode $domNode, Cursor $cursor): NodeInterface
	{
		return new LinkTagNode($cursor->node);
	}
	
	/**
	 * @return string[]
	 */
	public function getDefaultAllowedAttributes(): array
	{
		return ['href', 'class'];
	}
	
	/**
	 * @return string
	 */
	protected function getDomNodeName(): string
	{
		return 'a';
	}
}