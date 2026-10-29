<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CoachController extends AbstractController
{
    #[Route('/coaches', name: 'app_coaches')]
    public function index(): Response
    {
        $featuredCoach = [
            'name' => 'ZywOo',
            'role' => 'Grandmaster Occult Tactician',
            'avatar' => 'images/zywoo.jpg',
            'banner' => 'images/background.png',
            'bio' => 'Former competitive champion specializing in high-level match momentum, positioning, occult item prioritization, and clutch late-game team fights.',
            'sessions' => [
                [
                    'title' => 'VOD Replay In-Depth Breakdown',
                    'desc' => '60 min comprehensive review of your decision-making and occult pathing.',
                    'price' => '$45',
                ],
                [
                    'title' => 'Live Match Duo & Real-time Coaching',
                    'desc' => '90 min live guidance on lane dominance, active communication, and target calls.',
                    'price' => '$80',
                ],
            ],
            'reviews' => [
                [
                    'user' => 'NightStalker99',
                    'rating' => '5.0',
                    'time' => '2 days ago',
                    'text' => 'Incredible coaching! Explained lane mechanics and item counters that completely changed my win rate.',
                ],
                [
                    'user' => 'OccultWraith',
                    'rating' => '5.0',
                    'time' => '1 week ago',
                    'text' => 'Climbed two brackets right after the session. Best Deadlock coaching available.',
                ],
            ],
        ];

        $proCoaches = [
            [
                'name' => 'JohnDoe',
                'role' => 'Top Tank Specialist',
                'avatar' => 'images/john.jpg',
                'banner' => 'images/background.png',
                'rank' => '1',
                'rating' => '5.0 ★ (120+ sessions)',
                'bio' => 'Master advanced positioning, peeling, and crowd control chains to dominate high-MMR lobbies.',
                'price' => '$35/hr',
            ],
            [
                'name' => 'JaneSmith',
                'role' => 'Occult Support Analyst',
                'avatar' => 'images/jane.jpg',
                'banner' => 'images/background.png',
                'rank' => '2',
                'rating' => '4.9 ★ (95+ sessions)',
                'bio' => 'Heal, buff, and empower your core heroes with flawless map awareness and timing.',
                'price' => '$35/hr',
            ],
            [
                'name' => 'Daniel',
                'role' => 'Weapon & Gun Carry Coach',
                'avatar' => 'images/daniel.jpg',
                'banner' => 'images/background.png',
                'rank' => '3',
                'rating' => '4.9 ★ (88+ sessions)',
                'bio' => 'Focus on weapon aiming, split-pushing, item thresholds, and game-ending objective sieges.',
                'price' => '$40/hr',
            ],
        ];

        $communityCoaches = [
            [
                'rank' => '#04',
                'name' => 'Donk',
                'avatar' => 'images/donk.jpg',
                'rating' => '4.8 ★',
                'bio' => 'Hyper-aggressive initiation and opening lane trades for solo queue dominance.',
                'price' => '$25/hr',
            ],
            [
                'rank' => '#05',
                'name' => 'Eido',
                'avatar' => 'images/eido.jpg',
                'rating' => '4.8 ★',
                'bio' => 'Map rotations, urn deliveries, and camp farming optimization.',
                'price' => '$22/hr',
            ],
            [
                'rank' => '#06',
                'name' => 'Lomein',
                'avatar' => 'images/lomein.jpg',
                'rating' => '4.7 ★',
                'bio' => 'Foundational mechanics and beginner-to-intermediate climb strategies.',
                'price' => '$20/hr',
            ],
            [
                'rank' => '#07',
                'name' => 'Mille',
                'avatar' => 'images/mille.jpg',
                'rating' => '4.7 ★',
                'bio' => 'Support utility items, active cooldown usage, and defensive positioning.',
                'price' => '$20/hr',
            ],
            [
                'rank' => '#08',
                'name' => 'OHDM',
                'avatar' => 'images/OHDM.jpg',
                'rating' => '4.9 ★',
                'bio' => 'Spirit builds, ability scaling, and maximizing burst damage combos.',
                'price' => '$25/hr',
            ],
            [
                'rank' => '#09',
                'name' => 'Saiah',
                'avatar' => 'images/saiah.jpg',
                'rating' => '4.8 ★',
                'bio' => 'Sniper mechanics, Vindicta and Grey Talon long-range occult execution.',
                'price' => '$25/hr',
            ],
        ];

        return $this->render('coach/index.html.twig', [
            'featured' => $featuredCoach,
            'pro_coaches' => $proCoaches,
            'community_coaches' => $communityCoaches,
        ]);
    }
}

