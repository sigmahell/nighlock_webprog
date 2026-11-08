<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rating', ChoiceType::class, [
                'label' => 'Rating',
                'choices' => [
                    '★ ★ ★ ★ ★ (5 Stars - Outstanding)' => 5,
                    '★ ★ ★ ★ ☆ (4 Stars - Very Good)'   => 4,
                    '★ ★ ★ ☆ ☆ (3 Stars - Good)'        => 3,
                    '★ ★ ☆ ☆ ☆ (2 Stars - Fair)'        => 2,
                    '★ ☆ ☆ ☆ ☆ (1 Star - Poor)'         => 1,
                ],
                'placeholder' => '-- Select a rating --',
                'attr' => [
                    'class' => 'form-input',
                ],
                'constraints' => [
                    new NotBlank(message: 'Please choose a rating.'),
                ],
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Your Feedback / Review',
                'attr' => [
                    'placeholder' => 'Share your experience with this coach (how did they help you, what did you improve on?)...',
                    'rows' => 4,
                    'class' => 'form-input',
                ],
                'constraints' => [
                    new NotBlank(message: 'Please provide a short review comment.'),
                    new Length(
                        min: 5,
                        minMessage: 'Your review comment should be at least {{ limit }} characters long.'
                    ),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
