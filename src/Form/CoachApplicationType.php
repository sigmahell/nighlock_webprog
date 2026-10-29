<?php

namespace App\Form;

use App\Entity\Coach;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CoachApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Coach Display Name',
                'attr' => [
                    'placeholder' => 'e.g. ShadowStrike or your Steam Alias',
                    'class' => 'form-input',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Please provide a coach display name.']),
                    new Length(['min' => 2, 'max' => 100]),
                ],
            ])
            ->add('specialization', TextType::class, [
                'label' => 'Specialization / Role Title',
                'attr' => [
                    'placeholder' => 'e.g. Top Tank Coach, Spirit Infernus Specialist, Support Peel',
                    'class' => 'form-input',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Please specify your coaching specialty.']),
                    new Length(['max' => 100]),
                ],
            ])
            ->add('hourlyRate', TextType::class, [
                'label' => 'Hourly Rate',
                'attr' => [
                    'placeholder' => 'e.g. $20.00 / hr',
                    'class' => 'form-input',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Please provide your expected hourly rate.']),
                ],
            ])
            ->add('bio', TextareaType::class, [
                'label' => 'Experience & Coaching Bio',
                'attr' => [
                    'placeholder' => 'Describe your Deadlock experience, peak MMR/rank, heroes you coach, and your coaching approach...',
                    'rows' => 5,
                    'class' => 'form-input',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Please provide a brief bio describing your experience.']),
                    new Length(['min' => 20, 'minMessage' => 'Bio should be at least 20 characters.']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Coach::class,
        ]);
    }
}
