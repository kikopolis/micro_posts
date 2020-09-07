<?php

declare(strict_types = 1);

namespace App\Form;

use App\Entity\UserProfile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProfileEditType extends AbstractType
{
	/**
	 * @param  FormBuilderInterface  $builder
	 * @param  array                 $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add(
				'avatar',
				FileType::class,
				[
					'required'    => false,
					'label'       => 'Your avatar',
					'mapped'      => false,
					'constraints' =>
						[
							new File(
								[
									'maxSize'        => '1024k',
									'maxSizeMessage' => 'File maximum size is 1MB',
									'mimeTypes'      => [
										'image/jpeg',
										'image/png',
										'image/webp',
									],
								]
							),
						],
				]
			)
			->add(
				'birthday',
				BirthdayType::class,
				[
					'required' => false,
				]
			)
			->add(
				'bio',
				TextareaType::class,
				[
					'required' => false,
				]
			)
			->add('submit_changes', SubmitType::class)
		;
	}
	
	/**
	 * @param  OptionsResolver  $resolver
	 */
	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults(
			[
				'data_class' => UserProfile::class,
			]
		);
	}
}