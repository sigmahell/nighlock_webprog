<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        // Handle contact form submission
        if ($request->isMethod('POST')) {
            $name = trim((string) $request->request->get('name'));
            $email = trim((string) $request->request->get('email'));
            $message = trim((string) $request->request->get('message'));

            if (!empty($name) && !empty($email) && !empty($message)) {
                $this->addFlash('success', 'Thank you, ' . htmlspecialchars($name) . '! Your occult inquiry has been received.');
                return $this->redirectToRoute('app_home', ['_fragment' => 'contact']);
            }

            $this->addFlash('error', 'Please fill in all fields before submitting.');
        }

        // Daily hero rotation seed (consistent throughout the day)
        mt_srand((int) date('Ymd'));

        $sTierHeroes = [
            ['name' => 'Seven', 'img' => 'images/hero_icons/Seven_card.png'],
            ['name' => 'Infernus', 'img' => 'images/hero_icons/Infernus_card.png'],
        ];

        $aTierHeroes = [
            ['name' => 'Vindicta', 'img' => 'images/hero_icons/Vindicta_card.png'],
            ['name' => 'Wraith', 'img' => 'images/hero_icons/Wraith_card.png'],
        ];

        $bTierHeroes = [
            ['name' => 'Pocket', 'img' => 'images/hero_icons/Pocket_card.png'],
            ['name' => 'Dynamo', 'img' => 'images/hero_icons/Dynamo_card.png'],
        ];

        // Shuffle arrays for daily rotation
        shuffle($sTierHeroes);
        shuffle($aTierHeroes);
        shuffle($bTierHeroes);

        // Featured items
        $items = [
            [
                'name' => 'Split Shot',
                'icon' => 'images/Split_Shot.png',
                'description' => 'Make your weapon fire multishot. Hitting more than one Hero per attack will grant a stacking weapon damage bonus.',
            ],
            [
                'name' => 'Indomitable',
                'icon' => 'images/Indomitable.png',
                'description' => 'The next Stun, Chain, Immobilize, Sleep or Silence is automatically cleansed. When this happens, you gain a barrier and all your ability cooldowns get reduced by 20%.',
            ],
            [
                'name' => 'Spirit Burn',
                'icon' => 'images/Spirit_Burn.png',
                'description' => 'Dealing significant spirit damage to an enemy within 5s causes an explosion dealing damage and a burn to that enemy. While burning, enemies take damage over time and receive reduced healing.',
            ],
        ];

        // Featured coaches
        $coaches = [
            [
                'name' => 'JohnDoe',
                'role' => 'Top Tank Coach',
                'image' => 'images/john.jpg',
                'bio' => 'Master and Learn advanced positioning and map awareness with me, defend and peel your team to victory.',
            ],
            [
                'name' => 'JaneSmith',
                'role' => 'Top Support Coach',
                'image' => 'images/jane.jpg',
                'bio' => 'Master and Learn advanced positioning and map awareness with me. Heal and support your team to victory.',
            ],
        ];

        return $this->render('home/index.html.twig', [
            's_tier_heroes' => $sTierHeroes,
            'a_tier_heroes' => $aTierHeroes,
            'b_tier_heroes' => $bTierHeroes,
            'items' => $items,
            'coaches' => $coaches,
        ]);
    }
}
