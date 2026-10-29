<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Coach;
use App\Entity\CoachSession;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $coach = $options['coach'];

        $builder
            ->add('session', EntityType::class, [
                'class' => CoachSession::class,
                'choice_label' => function (CoachSession $session) {
                    return sprintf('%s (%d mins) - %s', $session->getTitle(), $session->getDurationMinutes(), $session->getPrice());
                },
                'placeholder' => '-- Select a Coaching Package --',
                'required' => true,
                'query_builder' => function (EntityRepository $er) use ($coach) {
                    $qb = $er->createQueryBuilder('s');
                    if ($coach instanceof Coach) {
                        $qb->andWhere('s.coach = :coach')
                           ->setParameter('coach', $coach);
                    }
                    return $qb->orderBy('s.price', 'ASC');
                },
                'attr' => [
                    'class' => 'form-input',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Please select a session package.']),
                ],
            ])
            ->add('scheduledAt', DateTimeType::class, [
                'label' => 'Session Schedule (Date & Time)',
                'widget' => 'single_text',
                'html5' => true,
                'input' => 'datetime_immutable',
                'attr' => [
                    'class' => 'form-input',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Please select a scheduled date and time.']),
                    new GreaterThan([
                        'value' => 'now',
                        'message' => 'Your scheduled session must be in the future.',
                    ]),
                ],
            ])
            ->add('clientNotes', TextareaType::class, [
                'label' => 'Session Goals & Discord Handle',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Enter your Discord tag, main heroes, and what specific mechanics you want to improve (e.g. laning, positioning, item builds)...',
                    'rows' => 4,
                    'class' => 'form-input',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
            'coach' => null,
        ]);
        $resolver->setAllowedTypes('coach', ['null', Coach::class]);
    }
}
