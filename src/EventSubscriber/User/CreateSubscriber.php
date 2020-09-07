<?php

declare(strict_types = 1);

namespace App\EventSubscriber\User;

use App\Entity\UserPreferences;
use App\Entity\UserProfile;
use App\Event\User\CreateEvent;
use App\Service\Contracts\MailerContract;
use App\Service\Contracts\TokenGeneratorContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class CreateSubscriber
 * @package App\EventSubscriber\User
 */
class CreateSubscriber implements EventSubscriberInterface
{
	/**
	 * @var TokenGeneratorContract
	 */
	private TokenGeneratorContract $tokenGenerator;
	
	/**
	 * @var MailerContract
	 */
	private MailerContract $mailer;
	
	/**
	 * @var EntityManagerInterface
	 */
	private EntityManagerInterface $entityManager;
	
	/**
	 * @var UserProfile
	 */
	private UserProfile $profile;
	
	/**
	 * @var UserPreferences
	 */
	private UserPreferences $preferences;
	
	/**
	 * UserCreatedSubscriber constructor.
	 * @param  TokenGeneratorContract  $tokenGenerator
	 * @param  MailerContract          $mailer
	 * @param  EntityManagerInterface  $entityManager
	 * @param  UserProfile             $profile
	 * @param  UserPreferences         $preferences
	 */
	public function __construct(
		TokenGeneratorContract $tokenGenerator,
		MailerContract $mailer,
		EntityManagerInterface $entityManager,
		UserProfile $profile,
		UserPreferences $preferences
	)
	{
		$this->tokenGenerator = $tokenGenerator;
		$this->mailer         = $mailer;
		$this->entityManager  = $entityManager;
		$this->profile        = $profile;
		$this->preferences    = $preferences;
	}
	
	/**
	 * @return array|array[]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			CreateEvent::class => [
				['token', 999],
				['activationEmail', 998],
				['profile', 997],
				['preferences', 996],
			],
		];
	}
	
	/**
	 * @param  CreateEvent  $event
	 */
	public function token(CreateEvent $event): void
	{
		$event->getUser()->setAccountActivationToken(
			$this->tokenGenerator->generate()
		)
		;
	}
	
	/**
	 * @param  CreateEvent  $event
	 */
	public function activationEmail(CreateEvent $event): void
	{
		$this->mailer->activationInfo($event->getUser());
	}
	
	/**
	 * @param  CreateEvent  $event
	 */
	public function profile(CreateEvent $event): void
	{
		$user = $event->getUser();
		
		if (! $user->getProfile()) {
			
			$user->setProfile($this->profile);
			
			$this->entityManager->persist($this->profile);
		}
	}
	
	/**
	 * @param  CreateEvent  $event
	 */
	public function preferences(CreateEvent $event): void
	{
		$user = $event->getUser();
		
		if (! $user->getPreferences()) {
			
			$user->setPreferences($this->preferences);
			
			$this->entityManager->persist($this->preferences);
		}
	}
}