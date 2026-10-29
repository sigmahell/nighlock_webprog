<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        $user = [
            'username' => 'OccultAcolyte',
            'email' => 'acolyte@nightlock.gg',
            'initial' => 'O',
            'member_since' => 'October 2026',
        ];

        $bookings = [
            [
                'coach' => 'JohnDoe',
                'role' => 'Top Tank Specialist',
                'session' => '1-on-1 Positioning & Peeling',
                'date' => 'October 28, 2026 - 18:00 UTC',
                'status' => 'Confirmed',
            ],
            [
                'coach' => 'ZywOo',
                'role' => 'Grandmaster Occult Tactician',
                'session' => 'VOD Replay In-Depth Breakdown',
                'date' => 'November 02, 2026 - 20:00 UTC',
                'status' => 'Pending Confirmation',
            ],
        ];

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'bookings' => $bookings,
        ]);
    }
}

