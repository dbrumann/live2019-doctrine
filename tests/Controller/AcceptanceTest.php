<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group acceptance
 */
class AcceptanceTest extends WebTestCase
{
    public function testSignup()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('Register')->form([
            'displayname' => 'Bob',
            'username' => 'bob@example.com',
            'password' => 'secret',
        ]);
        $client->submit($form);

        $response = $client->getResponse();

        self::assertTrue($response->isRedirect('/login'));
    }

    /**
     * @depends testSignup
     */
    public function testCreatingLists()
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/login');

        // Perform login
        $form = $crawler->selectButton('Login')->form([
            '_username' => 'bob@example.com',
            '_password' => 'secret',
        ]);
        $crawler = $client->submit($form);

        // Create list
        $form = $crawler->selectButton('Create a list')->form([
            'name' => 'Our new todo app',
        ]);
        $crawler = $client->submit($form);

        // Beware of "|title" filter when comparing list titles
        self::assertSame('Our New Todo App', $crawler->filter('h5.card-title > a')->text());
    }

    /**
     * @depends testCreatingLists
     */
    public function testCreatingTasks()
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/login');

        // Perform login
        $form = $crawler->selectButton('Login')->form([
            '_username' => 'bob@example.com',
            '_password' => 'secret',
        ]);
        $crawler = $client->submit($form);

        // Display list
        $link = $crawler->filter('h5.card-title > a')->link();
        $crawler = $client->click($link);

        // Add task to list
        $form = $crawler->selectButton('Add task')->form([
            'summary' => 'Invite people to list',
        ]);
        $crawler = $client->submit($form);

        // Beware of "|title" filter when comparing task summaries
        self::assertSame('Invite People To List', $crawler->filter('.list-group-item strong')->text());
    }
}
