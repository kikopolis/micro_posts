<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Entity\Concerns\TimeStampableConcern;
use App\Entity\Contracts\TimeStampableContract;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VisitorsRepository")
 */
class Visitor implements TimeStampableContract
{
	use TimeStampableConcern;
	
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="bigint")
	 * @var null|int
	 */
	protected ?int $id = null;
	
	/**
	 * @ORM\Column(type="string", nullable=false)
	 * @var null|string
	 */
	protected ?string $clientIp = null;
	
	/**
	 * @ORM\Column(type="string", nullable=false)
	 * @var null|string
	 */
	protected ?string $route = null;
	
	/**
	 * @ORM\Column(type="string", nullable=false)
	 * @var null|string
	 */
	protected ?string $controller = null;
	
	/**
	 * @ORM\Column(type="string", nullable=false)
	 * @var null|string
	 */
	protected ?string $browser = null;
	
	/**
	 * @ORM\Column(type="datetime", nullable=false)
	 * @var null|DateTimeInterface
	 */
	protected ?DateTimeInterface $createdAt = null;
	
	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 * @var null|DateTimeInterface
	 */
	protected ?DateTimeInterface $updatedAt = null;
	
	/**
	 * Visitor constructor.
	 * @param  null|string  $clientIp
	 * @param  null|string  $route
	 * @param  null|string  $controller
	 * @param  null|string  $browser
	 */
	public function __construct(
		?string $clientIp = null,
		?string $route = null,
		?string $controller = null,
		?string $browser = null
	)
	{
		$this->clientIp   = $clientIp;
		$this->route      = $route;
		$this->controller = $controller;
		$this->browser    = $browser;
	}
	
	/**
	 * @return int|null
	 */
	public function getId(): ?int
	{
		return $this->id;
	}
	
	/**
	 * @return null|string
	 */
	public function getClientIp(): ?string
	{
		return $this->clientIp;
	}
	
	/**
	 * @param  null|string  $clientIp
	 * @return $this|Visitor
	 */
	public function setClientIp(?string $clientIp): Visitor
	{
		$this->clientIp = $clientIp;
		
		return $this;
	}
	
	/**
	 * @return null|string
	 */
	public function getRoute(): ?string
	{
		return $this->route;
	}
	
	/**
	 * @param  null|string  $route
	 * @return $this|Visitor
	 */
	public function setRoute(?string $route): Visitor
	{
		$this->route = $route;
		
		return $this;
	}
	
	/**
	 * @return null|string
	 */
	public function getController(): ?string
	{
		return $this->controller;
	}
	
	/**
	 * @param  null|string  $controller
	 * @return $this|Visitor
	 */
	public function setController(?string $controller): Visitor
	{
		$this->controller = $controller;
		
		return $this;
	}
	
	/**
	 * @return null|string
	 */
	public function getBrowser(): ?string
	{
		return $this->browser;
	}
	
	/**
	 * @param  null|string  $browser
	 * @return $this|Visitor
	 */
	public function setBrowser(?string $browser): Visitor
	{
		$this->browser = $browser;
		
		return $this;
	}
}
