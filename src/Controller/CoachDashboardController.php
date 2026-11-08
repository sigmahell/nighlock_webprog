<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Notification;
use App\Entity\User;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class CoachDashboardController extends AbstractController
{
    #[Route('/coach/dashboard', name: 'app_coach_dashboard', methods: ['GET'])]
    public function index(BookingRepository $bookingRepository): Response
    {
        $coach = $this->getApprovedCoach();

        return $this->render('coach/dashboard.html.twig', [
            'coach' => $coach,
            'bookings' => $bookingRepository->findByCoach($coach),
        ]);
    }

    #[Route('/coach/bookings/{id}/{action}', name: 'app_coach_booking_update', methods: ['POST'], requirements: ['action' => 'accept|deny|complete'])]
    public function updateBooking(
        Booking $booking,
        string $action,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $coach = $this->getApprovedCoach();

        if ($booking->getCoach() !== $coach) {
            throw $this->createAccessDeniedException('You cannot manage another coach\'s booking.');
        }

        if (!$this->isCsrfTokenValid('coach_booking_' . $booking->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid booking request.');
        }

        $transitions = [
            'accept' => ['from' => 'pending', 'to' => 'accepted', 'title' => 'Booking Accepted', 'message' => 'Your coaching request has been accepted.'],
            'deny' => ['from' => 'pending', 'to' => 'denied', 'title' => 'Booking Declined', 'message' => 'Your coaching request was declined by the coach.'],
            'complete' => ['from' => 'accepted', 'to' => 'completed', 'title' => 'Session Completed', 'message' => 'Your coaching session has been marked as completed.'],
        ];
        $transition = $transitions[$action];

        if ($booking->getStatus() !== $transition['from']) {
            $this->addFlash('error', 'This booking can no longer be updated with that action.');

            return $this->redirectToRoute('app_coach_dashboard');
        }

        $booking->setStatus($transition['to']);
        $notification = new Notification();
        $notification->setRecipient($booking->getClient());
        $notification->setBooking($booking);
        $notification->setType('booking_' . $transition['to']);
        $notification->setTitle($transition['title']);
        $notification->setMessage($transition['message']);
        $entityManager->persist($notification);
        $entityManager->flush();

        $this->addFlash('success', sprintf('Booking marked as %s.', $transition['to']));

        return $this->redirectToRoute('app_coach_dashboard');
    }

    private function getApprovedCoach(): \App\Entity\Coach
    {
        $user = $this->getUser();
        if (!$user instanceof User || $user->getCoachProfile() === null || $user->getCoachProfile()->getStatus() !== 'approved') {
            throw $this->createAccessDeniedException('Only approved coaches can access the coach dashboard.');
        }

        return $user->getCoachProfile();
    }
}
