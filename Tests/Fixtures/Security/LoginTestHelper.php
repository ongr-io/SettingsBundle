<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Fixtures\Security;

use Symfony\Bundle\FrameworkBundle\Client;

/**
 * Helper for test login.
 */
class LoginTestHelper
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Performs login action and returns same client after log in.
     *
     * @param string $username
     * @param string $password
     *
     * @return Client
     */
    public function loginAction($username = 'test', $password = 'test')
    {
        $client = $this->getClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/settings/login');
        $buttonNode = $crawler->selectButton('login_submit');

        $form = $buttonNode->form();
        $form['login[username]'] = $username;
        $form['login[password]'] = $password;
        $client->submit($form);

        return $client;
    }

    /**
     * Performs logout action and returns same client after log out.
     *
     * @param Client $client
     *
     * @return Client
     */
    public function logoutAction(Client $client)
    {
        $crawler = $client->request('GET', '/settings/login');
        $link = $crawler->filter('a:contains("Logout")')->link();
        $client->click($link);

        return $client;
    }

    /**
     * Get Client.
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }
}
