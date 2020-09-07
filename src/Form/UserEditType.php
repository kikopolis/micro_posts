<?php

declare(strict_types = 1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

/**
 * Class UserEditType
 * @package App\Form
 */
class UserEditType extends AbstractType
{
	/**
	 * @var Security
	 */
	private Security $security;
	
	/**
	 * UserEditType constructor.
	 * @param  Security  $security
	 */
	public function __construct(Security $security)
	{
		$this->security = $security;
	}
	
	/**
	 * @param  FormBuilderInterface  $builder
	 * @param  array                 $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		/** @var User $currentUser */
		$currentUser = $this->security->getUser();
		
		$builder
			->add(
				'email',
				EmailType::class,
				['required' => false]
			)
			->add(
				'fullName',
				TextType::class,
				['required' => false]
			)
		;
		
		if ($builder->getData()->getId() === $currentUser->getId()) {
			
			$builder
				->add(
					'plainPassword',
					PasswordType::class,
					[
						'label'    => 'Password. *(leave empty to keep the same)',
						'help'     => 'The password must be at least 8 characters, contain 1 lowercase, 1 uppercase and 1 number.',
						'required' => false,
					]
				)
				->add(
					'retypedPlainPassword',
					PasswordType::class,
					[
						'label'    => 'Retype password. *(leave empty to keep the same)',
						'required' => false,
					]
				)
			;
		}
		
		if ($currentUser && $currentUser->hasRole(User::ROLE_SUPER_ADMINISTRATOR)) {
			
			$builder->add(
				'roles',
				ChoiceType::class,
				[
					'label'    => 'User role :',
					'mapped'   => true,
					'required' => false,
					'expanded' => true,
					'multiple' => true,
					'choices'  => [
						'regular user'        => User::ROLE_USER,
						'moderator'           => User::ROLE_MODERATOR,
						'administrator'       => User::ROLE_ADMINISTRATOR,
						'super administrator' => User::ROLE_SUPER_ADMINISTRATOR,
					],
				]
			);
		}
		
		$builder->add('submit', SubmitType::class);
	}
	
	/**
	 * @param  OptionsResolver  $resolver
	 */
	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults(
			[
				'data_class'        => User::class,
				'validation_groups' => ['edit'],
			]
		);
	}
}