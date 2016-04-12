<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Functional\Controller;

use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use ONGR\SettingsBundle\Tests\Fixtures\Security\LoginTestHelper;
use Symfony\Bundle\FrameworkBundle\Client;

/**
 * Tests for SettingsController.
 */
class PersonalSettingsControllerTest extends AbstractElasticsearchTestCase
{
    /**
     * @var Client.
     */
    private $client;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    /**
     * Test settings page ability to set values to cookie.
     */
    public function testSettingsAction()
    {
        $this->getManager();
        /** @var Client $client */
        $client = $this->client;

        // Visit settings page.
        $crawler = $client->request('GET', '/settings/settings');

        // Assert categories are rendered.
        /** @var array $categories */
        $categories = $client->getContainer()->getParameter('ongr_settings.settings.categories');
        $content = $client->getResponse()->getContent();
        unset($categories['ongr_settings_profiles']);

        // Print $content.
        foreach ($categories as $category) {
            $this->assertContains($category['name'], $content);
        }

    }
}
