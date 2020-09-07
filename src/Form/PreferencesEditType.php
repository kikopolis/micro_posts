<?php

declare(strict_types = 1);

namespace App\Form;

use App\Entity\UserPreferences;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PreferencesEditType
 * @package App\Form
 */
class PreferencesEditType extends AbstractType
{
	/**
	 * @param  FormBuilderInterface  $builder
	 * @param  array                 $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add(
				'sortHomePageBy',
				ChoiceType::class,
				[
					'required' => false,
					//					'mapped'  => false,
					'choices'  => [
						'Show all posts, newest first'               => UserPreferences::SORT_BY_ALL_POSTS_NEWEST_FIRST,
						'Show posts by users I follow, newest first' => UserPreferences::SORT_BY_FOLLOWED_USERS_NEWEST_FIRST,
					],
				]
			)
			->add('save_changes', SubmitType::class)
		;
	}
	
	/**
	 * @param  OptionsResolver  $resolver
	 */
	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults(
			[
				'data_class' => UserPreferences::class,
			]
		);
	}
}