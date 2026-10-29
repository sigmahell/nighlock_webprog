<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class HomeControllerTest extends WebTestCase
{
    public function testHomePageLoadsSuccessfully(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('#title-card h1', 'Welcome to NightLock');
        $this->assertSelectorExists('#title-card');
        $this->assertSelectorExists('#tier-list');
        $this->assertSelectorExists('#items');
        $this->assertSelectorExists('#coach');
        $this->assertSelectorExists('#contact');
    }

    public function testContactFormSubmission(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $form = $crawler->filter('#contact form')->form([
            'name' => 'DeadlockPlayer',
            'email' => 'player@example.com',
            'message' => 'Need help with Vindicta spirit build!',
        ]);

        $client->submit($form);
        $this->assertResponseRedirects('/#contact');

        $crawler = $client->followRedirect();
        $this->assertSelectorTextContains('body', 'Thank you, DeadlockPlayer! Your occult inquiry has been received.');
    }
}
