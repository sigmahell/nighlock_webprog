<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_dashboard')]
    public function dashboard(): Response
    {
        $stats = [
            'total_users' => 128,
            'active_bookings' => 14,
            'completed_sessions' => 86,
            'registered_coaches' => 9,
        ];

        $recentInquiries = [
            ['name' => 'DeadlockPlayer', 'email' => 'player@example.com', 'message' => 'Need help with Vindicta spirit build!', 'date' => 'Today'],
            ['name' => 'ShadowShooter', 'email' => 'shadow@occult.io', 'message' => 'Question about booking group coaching.', 'date' => 'Yesterday'],
            ['name' => 'ApexOccultist', 'email' => 'apex@deadlock.gg', 'message' => 'Feedback on S-tier hero rotation.', 'date' => '2 days ago'],
        ];

        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats,
            'inquiries' => $recentInquiries,
        ]);
    }
}

