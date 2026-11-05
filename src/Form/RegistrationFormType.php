<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email Address',
                'attr'  => [
                    'class'       => 'form-input',
                    'placeholder' => 'player@nightlock.gg',
                    'autofocus'   => true,
                ],
                'constraints' => [
                    new NotBlank(message: 'Please enter your email.'),
                    new Email(message: 'Please enter a valid email address.'),
                ],
            ])
            ->add('steamName', TextType::class, [
                'label' => 'Steam Display Name',
                'attr'  => [
                    'class'       => 'form-input',
                    'placeholder' => 'e.g. OccultVanguard',
                ],
                'constraints' => [
                    new NotBlank(message: 'Please enter your Steam display name.'),
                    new Length(
                        min: 2,
                        max: 100,
                        minMessage: 'Steam name must be at least {{ limit }} characters.',
                        maxMessage: 'Steam name cannot exceed {{ limit }} characters.'
                    ),
                ],
            ])
            ->add('steamId', TextType::class, [
                'label'    => 'Steam ID (17-digit)',
                'required' => false,
                'attr'     => [
                    'class'       => 'form-input',
                    'placeholder' => 'e.g. 76561198000000000',
                    'maxlength'   => 17,
                ],
                'constraints' => [
                    new Regex(
                        pattern: '/^\d{17}$/',
                        message: 'Steam ID must be exactly 17 digits.'
                    ),
                ],
            ])
            // plainPassword is NOT mapped to the entity — we hash it manually
            ->add('plainPassword', RepeatedType::class, [
                'type'            => PasswordType::class,
                'mapped'          => false,
                'invalid_message' => 'The password fields must match.',
                'first_options'   => [
                    'label' => 'Password',
                    'attr'  => [
                        'class'        => 'form-input',
                        'autocomplete' => 'new-password',
                    ],
                    'constraints' => [
                        new NotBlank(message: 'Please enter a password.'),
                        new Length(
                            min: 8,
                            max: 4096,
                            minMessage: 'Password must be at least {{ limit }} characters.'
                        ),
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirm Password',
                    'attr'  => [
                        'class'        => 'form-input',
                        'autocomplete' => 'new-password',
                    ],
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
