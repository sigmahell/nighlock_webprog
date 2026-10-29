<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TierlistController extends AbstractController
{
    #[Route('/tierlist', name: 'app_tierlist')]
    public function index(): Response
    {
        $tiers = [
            'S' => [
                'label' => 'S TIER - OCCULT DOMINANCE',
                'class' => 'tier-s',
                'description' => 'Uncontested power picks with game-defining impact.',
                'heroes' => [
                    ['name' => 'Seven', 'img' => 'images/hero_icons/Seven_card.png'],
                    ['name' => 'Infernus', 'img' => 'images/hero_icons/Infernus_card.png'],
                    ['name' => 'Haze', 'img' => 'images/hero_icons/Haze_card.png'],
                    ['name' => 'Warden', 'img' => 'images/hero_icons/Warden_card.png'],
                    ['name' => 'Yamato', 'img' => 'images/hero_icons/Yamato_card.png'],
                ],
            ],
            'A' => [
                'label' => 'A TIER - SHADOW ARSENAL',
                'class' => 'tier-a',
                'description' => 'Formidable heroes with high carry potential and flexibility.',
                'heroes' => [
                    ['name' => 'Vindicta', 'img' => 'images/hero_icons/Vindicta_card.png'],
                    ['name' => 'Wraith', 'img' => 'images/hero_icons/Wraith_card.png'],
                    ['name' => 'Pocket', 'img' => 'images/hero_icons/Pocket_card.png'],
                    ['name' => 'Dynamo', 'img' => 'images/hero_icons/Dynamo_card.png'],
                    ['name' => 'Abrams', 'img' => 'images/hero_icons/Abrams_card.png'],
                    ['name' => 'Bebop', 'img' => 'images/hero_icons/Bebop_card.png'],
                    ['name' => 'Paradox', 'img' => 'images/hero_icons/Paradox_card.png'],
                ],
            ],
            'B' => [
                'label' => 'B TIER - BALANCED VANGUARD',
                'class' => 'tier-b',
                'description' => 'Solid performers viable across most team compositions.',
                'heroes' => [
                    ['name' => 'Grey Talon', 'img' => 'images/hero_icons/Grey_Talon_card.png'],
                    ['name' => 'Lady Geist', 'img' => 'images/hero_icons/Lady_Geist_card.png'],
                    ['name' => 'Lash', 'img' => 'images/hero_icons/Lash_card.png'],
                    ['name' => 'McGinnis', 'img' => 'images/hero_icons/McGinnis_card.png'],
                    ['name' => 'Mirage', 'img' => 'images/hero_icons/Mirage_card.png'],
                    ['name' => 'Mo & Krill', 'img' => 'images/hero_icons/Mo_&_Krill_card.png'],
                ],
            ],
            'C' => [
                'label' => 'C TIER - SITUATIONAL RITUALS',
                'class' => 'tier-c',
                'description' => 'Niche picks requiring specific compositions or game states.',
                'heroes' => [
                    ['name' => 'Shiv', 'img' => 'images/hero_icons/Shiv_card.png'],
                    ['name' => 'Viscous', 'img' => 'images/hero_icons/Viscous_card.png'],
                    ['name' => 'Kelvin', 'img' => 'images/hero_icons/Kelvin_card.png'],
                    ['name' => 'Ivy', 'img' => 'images/hero_icons/Ivy_card.png'],
                ],
            ],
            'D' => [
                'label' => 'D TIER - UNTESTED ACOLYTES',
                'class' => 'tier-d',
                'description' => 'Underperforming in current occult meta or experimental.',
                'heroes' => [
                    ['name' => 'Sinclair', 'img' => 'images/hero_icons/Sinclair_card.png'],
                    ['name' => 'Holliday', 'img' => 'images/hero_icons/Holliday_card.png'],
                    ['name' => 'Calico', 'img' => 'images/hero_icons/Calico_card.png'],
                ],
            ],
        ];

        return $this->render('tierlist/index.html.twig', [
            'tiers' => $tiers,
        ]);
    }
}

