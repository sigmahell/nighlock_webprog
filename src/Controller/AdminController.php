<?php

namespace App\Controller;

use App\Entity\Coach;
use App\Entity\CoachSession;
use App\Entity\Notification;
use App\Entity\Review;
use App\Entity\User;
use App\Repository\BookingRepository;
use App\Repository\CoachRepository;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
final class AdminController extends AbstractController
{
    #[Route('', name: 'app_admin_dashboard')]
    public function dashboard(
        UserRepository $userRepository,
        CoachRepository $coachRepository,
        BookingRepository $bookingRepository,
        ReviewRepository $reviewRepository,
    ): Response
    {
        $bookingStatistics = $bookingRepository->getBookingStatistics();
        $stats = [
            'total_users'        => $userRepository->count([]),
            'active_bookings'    => $bookingStatistics['pending'] + $bookingStatistics['accepted'],
            'completed_sessions' => $bookingRepository->count(['status' => 'completed']),
            'registered_coaches' => $coachRepository->count(['status' => 'approved']),
            'total_reviews'      => $reviewRepository->count([]),
        ];

        return $this->render('admin/dashboard.html.twig', [
            'stats'                => $stats,
            'pending_applications' => $coachRepository->findPendingApplications(),
            'users'                => $userRepository->findBy([], ['createdAt' => 'DESC']),
            'all_bookings'         => $bookingRepository->findBy([], ['scheduledAt' => 'DESC']),
            'recent_reviews'       => $reviewRepository->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/coach-applications/{id}/{decision}', name: 'app_admin_coach_application_decision', methods: ['POST'], requirements: ['decision' => 'approve|reject'])]
    public function decideCoachApplication(
        Coach $coach,
        string $decision,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        if (!$this->isCsrfTokenValid('coach_application_' . $coach->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid coach application request.');
        }

        if ($coach->getStatus() !== 'pending') {
            $this->addFlash('error', 'This coach application has already been reviewed.');
            return $this->redirectToRoute('app_admin_dashboard');
        }

        $approved = $decision === 'approve';
        $coach->setStatus($approved ? 'approved' : 'rejected');

        if ($approved && $coach->getSessions()->isEmpty()) {
            $session = new CoachSession();
            $session->setTitle('1-on-1 Coaching Session');
            $session->setDurationMinutes(60);
            $sessionPrice = preg_replace('/\/hr$/', '', $coach->getHourlyRate() ?? '₱500');
            $session->setPrice($sessionPrice);
            $session->setCoach($coach);
            $entityManager->persist($session);
        }

        if ($coach->getUser() !== null) {
            $notification = new Notification();
            $notification->setRecipient($coach->getUser());
            $notification->setType($approved ? 'application_approved' : 'application_rejected');
            $notification->setTitle($approved ? 'Coach Application Approved' : 'Coach Application Update');
            $notification->setMessage($approved
                ? 'Congratulations! Your NightLock coach application has been approved.'
                : 'Your NightLock coach application was not approved at this time.');
            $entityManager->persist($notification);
        }

        $entityManager->flush();
        $this->addFlash('success', sprintf('Coach application for %s was %s.', $coach->getName(), $approved ? 'approved' : 'rejected'));

        return $this->redirectToRoute('app_admin_dashboard');
    }

    #[Route('/users/{id}/toggle-role', name: 'app_admin_user_toggle_role', methods: ['POST'])]
    public function toggleAdminRole(
        User $user,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        if (!$this->isCsrfTokenValid('toggle_role_' . $user->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid request token.');
        }

        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if ($user === $currentUser) {
            $this->addFlash('error', 'You cannot change your own admin role.');
            return $this->redirectToRoute('app_admin_dashboard');
        }

        $roles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $roles, true)) {
            $roles = array_values(array_diff($roles, ['ROLE_ADMIN']));
            $this->addFlash('success', sprintf('Revoked admin privileges from %s.', $user->getEmail()));
        } else {
            $roles[] = 'ROLE_ADMIN';
            $this->addFlash('success', sprintf('Granted admin privileges to %s.', $user->getEmail()));
        }

        $user->setRoles($roles);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_dashboard');
    }

    #[Route('/reviews/{id}/delete', name: 'app_admin_review_delete', methods: ['POST'])]
    public function deleteReview(
        Review $review,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        if (!$this->isCsrfTokenValid('delete_review_' . $review->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid request token.');
        }

        $coachName = $review->getCoach() ? $review->getCoach()->getName() : 'Unknown';
        $entityManager->remove($review);
        $entityManager->flush();

        $this->addFlash('success', sprintf('Review for coach %s was deleted by admin.', $coachName));
        return $this->redirectToRoute('app_admin_dashboard');
    }
}
