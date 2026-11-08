<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Notification;
use App\Entity\Review;
use App\Entity\User;
use App\Form\ReviewType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class ReviewController extends AbstractController
{
    #[Route('/bookings/{id}/review', name: 'app_booking_review', methods: ['GET', 'POST'])]
    public function review(
        Booking $booking,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof User || $booking->getClient() !== $user) {
            throw $this->createAccessDeniedException('You can only review your own coaching sessions.');
        }

        if ($booking->getStatus() !== 'completed') {
            $this->addFlash('error', 'You can only review sessions that have been completed by the coach.');
            return $this->redirectToRoute('app_profile');
        }

        if ($booking->getReview() !== null) {
            $this->addFlash('info', 'You have already submitted a review for this session.');
            return $this->redirectToRoute('app_profile');
        }

        $review = new Review();
        $review->setBooking($booking);
        $review->setClient($user);
        $review->setCoach($booking->getCoach());

        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($review);

            // Optional: Notify coach of the review
            if ($booking->getCoach() && $booking->getCoach()->getUser()) {
                $notification = new Notification();
                $notification->setRecipient($booking->getCoach()->getUser());
                $notification->setBooking($booking);
                $notification->setType('review_received');
                $notification->setTitle('New Review Received');
                $notification->setMessage(
                    sprintf(
                        '%s left you a %d-star review: "%s"',
                        $user->getSteamName() ?? $user->getEmail(),
                        $review->getRating(),
                        mb_substr($review->getComment(), 0, 50) . (mb_strlen($review->getComment()) > 50 ? '...' : '')
                    )
                );
                $entityManager->persist($notification);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Thank you! Your review has been submitted.');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('review/new.html.twig', [
            'booking' => $booking,
            'coach'   => $booking->getCoach(),
            'form'    => $form->createView(),
        ]);
    }
}
