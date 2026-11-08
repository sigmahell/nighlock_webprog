<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class NavigationTest extends WebTestCase
{
    public function testNavigationRoutesLoadSuccessfully(): void
    {
        $client = static::createClient();

        // 1. Home page
        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('nav.nav-links');
        $this->assertSelectorExists('a[href="/tierlist"]');
        $this->assertSelectorExists('a[href="/coaches"]');
        $this->assertSelectorExists('a[href="/login"]');
        $this->assertSelectorExists('a[href="/register"]');

        // 2. Tierlist page
        $crawler = $client->request('GET', '/tierlist');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'DEADLOCK HERO TIER LIST');
        $this->assertSelectorExists('.tier-s');
        $this->assertSelectorExists('.tier-a');

        // 3. Coaches page
        $crawler = $client->request('GET', '/coaches');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('#featured-coaches h2', 'FEATURED COACH');
        $this->assertSelectorExists('#pro-coaches');
        $this->assertSelectorExists('#community-coaches');

        // 4. Login page
        $crawler = $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'SIGN IN TO NIGHTLOCK');

        // 5. Register page
        $crawler = $client->request('GET', '/register');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'CREATE AN OCCULT ACCOUNT');

        // 6. Anonymous visitors cannot access the admin dashboard.
        $crawler = $client->request('GET', '/admin');
        $this->assertResponseRedirects('/login');

        // 7. Anonymous visitors cannot access the profile page.
        $crawler = $client->request('GET', '/profile');
        $this->assertResponseRedirects('/login');

        // 8. The coach workspace is protected too.
        $crawler = $client->request('GET', '/coach/dashboard');
        $this->assertResponseRedirects('/login');

        // 9. Notifications are private to signed-in users.
        $crawler = $client->request('GET', '/notifications');
        $this->assertResponseRedirects('/login');
    }
}
