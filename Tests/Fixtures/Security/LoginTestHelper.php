<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Fixtures\Security;

use Symfony\Bundle\FrameworkBundle\Client;

/**
 * Helper for test login.
 */
class LoginTestHelper
{
    /**
     * @param Client $client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * Performs login action and returns same client after log in.
     *
     * @param string $username
     * @param string $password
     *
     * @return mixed $client
     */
    public function loginAction($username = 'test', $password = 'test')
    {
        $client = $this->getClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/admin/login');
        $buttonNode = $crawler->selectButton('login_submit');
        $form = $buttonNode->form();
        $form['login[username]'] = $username;
        $form['login[password]'] = $password;
        $client->submit($form);

        return $client;
    }

    public function getClient()
    {
        return $this->client;
    }
}
