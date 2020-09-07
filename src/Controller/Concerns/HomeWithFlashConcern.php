<?php

declare(strict_types = 1);

namespace App\Controller\Concerns;

use App\Service\Contracts\FlashContract;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Assumes inheritance of Symfony's AbstractController.
 * Trait SuccessRedirectConcern
 * @package App\Controller\Concerns
 */
trait HomeWithFlashConcern
{
	/**
	 * @param  string  $message
	 * @return RedirectResponse
	 */
	protected function successFlashHome(string $message): RedirectResponse
	{
		$this->addFlash(FlashContract::SUCCESS, $message);
		
		return $this->redirectToRoute('homepage');
	}
	
	/**
	 * @param  string  $message
	 * @return RedirectResponse
	 */
	protected function infoFlashHome(string $message): RedirectResponse
	{
		$this->addFlash(FlashContract::INFO, $message);
		
		return $this->redirectToRoute('homepage');
	}
	
	/**
	 * @param  string  $message
	 * @return RedirectResponse
	 */
	protected function errorFlashHome(string $message): RedirectResponse
	{
		$this->addFlash(FlashContract::ERROR, $message);
		
		return $this->redirectToRoute('homepage');
	}
	
	/**
	 * @param  string  $message
	 * @return RedirectResponse
	 */
	protected function warningFlashHome(string $message): RedirectResponse
	{
		$this->addFlash(FlashContract::WARNING, $message);
		
		return $this->redirectToRoute('homepage');
	}
}