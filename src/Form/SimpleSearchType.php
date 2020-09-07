<?php

declare(strict_types = 1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class SimpleSearchType extends AbstractType
{
	/**
	 * @param  FormBuilderInterface  $builder
	 * @param  array                 $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add(
				'query',
				SearchType::class,
				[
					'label'       => false,
					'attr'        => [
						'placeholder' => 'Search',
					],
					'constraints' =>
						[
							new NotBlank(),
						],
				]
			)
			->add('submit', SubmitType::class)
		;
	}
	
	/**
	 * @param  OptionsResolver  $resolver
	 */
	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([]);
	}
}
