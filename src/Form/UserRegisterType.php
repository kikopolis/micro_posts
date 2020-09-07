<?php

declare(strict_types = 1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Required;

/**
 * Class UserRegisterType
 * @package App\Form
 */
class UserRegisterType extends AbstractType
{
	/**
	 * @var RouterInterface
	 */
	private RouterInterface $router;
	
	/**
	 * UserRegisterType constructor.
	 * @param  RouterInterface  $router
	 */
	public function __construct(RouterInterface $router)
	{
		$this->router = $router;
	}
	
	/**
	 * @param  FormBuilderInterface  $builder
	 * @param  array                 $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add(
				'username',
				TextType::class,
				[
					'help' => 'Username must be unique.',
				]
			)
			->add(
				'fullname',
				TextType::class
			)
			->add(
				'email',
				EmailType::class
			)
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
			->add(
				'termsAgreed', CheckboxType::class, [
					             'mapped'      => false,
					             'constraints' =>
						             [
							             new IsTrue(),
							             new Required(),
						             ],
					             'label'       => 'I have read and agree to the terms of service.',
					             'help'        => "<a href=\"{$this->router->generate('terms.and.conditions')}\">Read the terms here!</a>",
					             'help_html'   => true,
				             ]
			)
			->add('register', SubmitType::class)
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