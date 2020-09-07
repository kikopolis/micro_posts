<?php

declare(strict_types = 1);

namespace App\Entity\Concerns;

use App\Security\Sanitizer\LinkTagExtension;
use HtmlSanitizer\Extension\Image\ImageExtension;
use HtmlSanitizer\Sanitizer;
use HtmlSanitizer\SanitizerBuilder;
use HtmlSanitizer\SanitizerInterface;

/**
 * Trait SanitizableConcern
 * @package App\Entity\Concerns
 */
trait SanitizableConcern
{
	/**
	 * @var null|SanitizerInterface
	 */
	protected ?SanitizerInterface $sanitizer = null;
	
	/**
	 * @var null|SanitizerInterface
	 */
	protected ?SanitizerInterface $purifier = null;
	
	/**
	 * @param  string  $dirty
	 * @return string
	 */
	public function sanitize(string $dirty): string
	{
		if (is_null($this->sanitizer)) {
			$san = new SanitizerBuilder();
			$san->registerExtension(new LinkTagExtension());
			$san->registerExtension(new ImageExtension());
			
			$this->sanitizer = $san->build(
				[
					'max_input_length' => 12000,
					'extensions'       => ['a', 'image'],
					'tags'             => [
						'a'   => [
						],
						'img' => [
							'allowed_hosts'  => null,
							'allow_data_uri' => false,
						],
					],
				]
			);
		}
		
		return $this->sanitizer->sanitize($dirty);
	}
	
	/**
	 * Completely purify the incoming $dirty of everything
	 * @param  string  $dirty
	 * @return string
	 */
	public function purify(string $dirty): string
	{
		if (is_null($this->purifier)) {
			
			$this->purifier = Sanitizer::create(
				[
					'max_input_length' => 12000,
				]
			);
		}
		
		return $this->purifier->sanitize($dirty);
	}
}