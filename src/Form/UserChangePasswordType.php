<?php

declare(strict_types = 1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserChangePasswordType
 * @package App\Form
 */
class UserChangePasswordType extends AbstractType
{
	/**
	 * @param  FormBuilderInterface  $builder
	 * @param  array                 $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add(
				'plainPassword',
				PasswordType::class,
				[
					'label' => 'Password',
					'help'  => 'The password must be at least 8 characters, contain 1 lowercase, 1 uppercase and 1 number.',
				]
			)
			->add(
				'retypedPlainPassword',
				PasswordType::class,
				[
					'label' => 'Retype password',
				]
			)
			->add('save_new_password', SubmitType::class)
		;
	}
	
	/**
	 * @param  OptionsResolver  $resolver
	 */
	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults(
			[
				'data_class' => User::class,
			]
		);
	}
}