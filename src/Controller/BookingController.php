<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Notification;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use App\Repository\CoachRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class BookingController extends AbstractController
{
    /**
     * Book a coaching session with a specific coach.
     * GET  → display the booking form
     * POST → handle form submission, persist booking + notification
     */
    #[Route('/coaches/{id}/book', name: 'app_booking_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(
        int $id,
        CoachRepository $coachRepository,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        // --- REPOSITORY: Fetch the chosen coach ---
        $coach = $coachRepository->find($id);

        if (!$coach || $coach->getStatus() !== 'approved') {
            throw $this->createNotFoundException('Coach not found or not available for booking.');
        }

        // --- FORM HANDLING: Booking Form ---
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking, [
            'coach' => $coach,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Bind the logged-in user as client and the selected coach
            $booking->setClient($this->getUser());
            $booking->setCoach($coach);
            $booking->setStatus('pending');

            // Snapshot the session title and price for history (even if session is later deleted)
            $selectedSession = $booking->getSession();
            if ($selectedSession) {
                $booking->setSessionTitle($selectedSession->getTitle());
                $booking->setPrice($selectedSession->getPrice());
            }

            $entityManager->persist($booking);

            // Notify the coach's user account about the new booking
            if ($coach->getUser()) {
                $notification = new Notification();
                $notification->setRecipient($coach->getUser());
                $notification->setBooking($booking);
                $notification->setType('new_booking');
                $notification->setTitle('New Booking Request');
                $notification->setMessage(
                    sprintf(
                        '%s has requested a coaching session: "%s" on %s.',
                        $this->getUser()->getSteamName() ?? $this->getUser()->getEmail(),
                        $booking->getSessionTitle(),
                        $booking->getScheduledAt()->format('M d, Y @ H:i')
                    )
                );
                $entityManager->persist($notification);
            }

            $entityManager->flush();

            $this->addFlash('success', sprintf(
                'Your booking request with %s has been submitted! They will confirm shortly.',
                $coach->getName()
            ));

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('booking/new.html.twig', [
            'coach' => $coach,
            'form'  => $form->createView(),
        ]);
    }

    /**
     * Cancel a booking (client-side).
     */
    #[Route('/bookings/{id}/cancel', name: 'app_booking_cancel', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function cancel(
        int $id,
        BookingRepository $bookingRepository,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        // --- REPOSITORY: Fetch the booking ---
        $booking = $bookingRepository->find($id);

        if (!$booking) {
            throw $this->createNotFoundException('Booking not found.');
        }

        // Security: only the client who made the booking can cancel it
        if ($booking->getClient() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot cancel this booking.');
        }

        // CSRF token check
        if ($this->isCsrfTokenValid('cancel_booking_' . $booking->getId(), $request->request->get('_token'))) {
            if (in_array($booking->getStatus(), ['pending', 'accepted'], true)) {
                $booking->setStatus('cancelled');
                $entityManager->flush();
                $this->addFlash('success', 'Booking cancelled successfully.');
            } else {
                $this->addFlash('error', 'This booking cannot be cancelled in its current state.');
            }
        }

        return $this->redirectToRoute('app_profile');
    }
}
