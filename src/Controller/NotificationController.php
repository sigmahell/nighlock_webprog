<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class NotificationController extends AbstractController
{
    #[Route('/notifications', name: 'app_notifications', methods: ['GET'])]
    public function index(NotificationRepository $notificationRepository): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('notification/index.html.twig', [
            'notifications' => $notificationRepository->findForRecipient($user),
            'unread_count' => $notificationRepository->countUnreadForRecipient($user),
        ]);
    }

    #[Route('/notifications/{id}/read', name: 'app_notification_read', methods: ['POST'])]
    public function markRead(
        Notification $notification,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $this->assertNotificationOwner($notification);

        if (!$this->isCsrfTokenValid('notification_' . $notification->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid notification request.');
        }

        $notification->setIsRead(true);
        $entityManager->flush();

        return $this->redirectToRoute('app_notifications');
    }

    #[Route('/notifications/read-all', name: 'app_notifications_read_all', methods: ['POST'])]
    public function markAllRead(
        NotificationRepository $notificationRepository,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof User || !$this->isCsrfTokenValid('notifications_read_all', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid notification request.');
        }

        foreach ($notificationRepository->findForRecipient($user) as $notification) {
            $notification->setIsRead(true);
        }
        $entityManager->flush();

        $this->addFlash('success', 'All notifications have been marked as read.');

        return $this->redirectToRoute('app_notifications');
    }

    private function assertNotificationOwner(Notification $notification): void
    {
        if ($notification->getRecipient() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot manage another user\'s notification.');
        }
    }
}
