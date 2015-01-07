<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Functional\Settings\Admin;

use ONGR\AdminBundle\Tests\Fixtures\Security\LoginTestHelper;
use ONGR\ElasticsearchBundle\ORM\Manager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Test integration with admin-user feature in admin bundle.
 */
class AdminSettingsTestSelected extends WebTestCase
{
    /**
     * @var Client
     */
    private $client;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        static::bootKernel(['environment' => 'test_container_creation']);

        // Log in.
        $client = self::createClient();
        $loginHelper = new LoginTestHelper($client);
        $this->client = $loginHelper->loginAction('test', 'test');
    }

    /**
     * When user is logged in, and he selects profile, then settings container must receive that choice.
     *
     * @runInSeparateProcess
     */
    public function testSettingsSelected()
    {
        // Retrieve content.
        $crawler = $this->client->request('GET', '/admin/settings');

        // Submit domain selection.
        $buttonNode = $crawler->selectButton('settings_submit');
        $form = $buttonNode->form();
        /** @noinspection PhpUndefinedMethodInspection */
        $form['settings[ongr_admin_profile_profile_foo-2e-com]']->tick();
        $this->client->submit($form);

        // Load any url and check that user selected domains are loaded.
        $this->client->request('GET', '/admin/setting/name0/edit');
        $settingsContainer = $this->client->getContainer()->get('ongr_admin.settings_container');

        $selectedDomains = $settingsContainer->getProfiles();
        $this->assertEquals(['default', 'test1', 'test2', 'meh', 'profile_foo.com'], $selectedDomains);
    }
}
