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

use ONGR\SettingsBundle\Tests\Fixtures\Security\LoginTestHelper;
use ONGR\SettingsBundle\Tests\Functional\PreparePersonalData;
use ONGR\SettingsBundle\Tests\Functional\PrepareAdminData;
use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

/**
 * Tests for SettingsController.
 */
class PersonalSettingsControllerTest extends ElasticsearchTestCase
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
        $this->client = new LoginTestHelper(static::createClient());
    }

    /**
     * Test settings page ability to set values to cookie.
     */
    public function testSettingsAction()
    {
        $client = $this->client->loginAction('test', 'test');

        // Visit settings page.
        $crawler = $client->request('GET', '/settings/settings');

        // Assert categories are rendered.
        /** @var array $categories */
        $categories = $client->getContainer()->getParameter('ongr_settings.settings.categories');
        $content = $client->getResponse()->getContent();

        // Print $content.
        foreach ($categories as $category) {
            $this->assertContains($category['name'], $content);
        }

        // Submit settings form.
        $buttonNode = $crawler->selectButton('settings_submit');
        $form = $buttonNode->form();

        /** @noinspection PhpUndefinedMethodInspection */
        $form['settings[foo_setting_1]']->tick();
        /** @noinspection PhpUndefinedMethodInspection */
        $form['settings[foo_setting_2]']->untick();
        /** @noinspection PhpUndefinedMethodInspection */
        $form['settings[foo_setting_3]']->tick();
        /** @noinspection PhpUndefinedMethodInspection */
        $form['settings[ongr_settings_profile_Acme2]']->tick();
        /** @noinspection PhpUndefinedMethodInspection */
        $form['settings[ongr_settings_profile_Acme1]']->tick();
        /** @noinspection PhpUndefinedMethodInspection */
        $form['settings[ongr_settings_live_settings]']->untick();
        $client->submit($form);

        // Assert successful redirect.
        $this->assertStringEndsWith(
            'settings',
            $client->getRequest()->getUri(),
            'response must be a correct redirect'
        );

        // Assert cookie values updated.
        $cookieValue = $client
            ->getCookieJar()
            ->get($client->getContainer()->getParameter('ongr_settings.settings.settings_cookie.name'))
            ->getValue();

        $expectedValue = [
            'foo_setting_1' => true,
            'foo_setting_2' => false,
            'foo_setting_3' => true,
            'ongr_settings_profile_Acme2' => true,
            'ongr_settings_profile_Acme1' => true,
            'ongr_settings_live_settings' => false,
        ];
        $this->assertJsonStringEqualsJsonString(json_encode($expectedValue), $cookieValue);

        // Try to change value through change setting action.
        $data[0] = ['foo_setting_1', false];
        $data[1] = ['foo_setting_non_existent', false ];

        foreach ($data as $case) {
            $client->request('get', '/admin/setting/change/' . base64_encode($case[0]));

            // Assert cookie values updated.
            $cookieValue = $client
                ->getCookieJar()
                ->get($client->getContainer()->getParameter('ongr_settings.settings.settings_cookie.name'))
                ->getValue();

            $this->assertJsonStringEqualsJsonString(json_encode($expectedValue), $cookieValue);
        }
    }

    /**
     * Settings pages should not be allowed to access non-authorized users, redirect should be initiated.
     */
    public function testActionsWhenNotLoggedInNoRedirect()
    {
        $client = $this->client->getClient();
        $client->followRedirects(false);

        // Visit settings page.
        $client->request('GET', '/settings/settings');

        // Assert access is redirected.
        $this->assertSame(302, $client->getResponse()->getStatusCode());
    }

    /**
     * Settings pages should not be allowed to access non-authorized users, user should be redirected this time.
     */
    public function testActionsWhenNotLoggedInRedirectToLogin()
    {
        $client = $this->client->getClient();
        $client->followRedirects(true);
        $client->request('GET', '/settings/logout');

        // Visit settings page.
        $client->request('GET', '/settings/settings');

        // Assert successful redirect when not logged in.
        $this->assertStringEndsWith(
            'login',
            $client->getRequest()->getUri(),
            'response must be a correct redirect'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getDataArray()
    {
        return [
            'default' => [
                'setting' => [
                    [
                        'name' => 'Acme1',
                        'profile' => 'Acme1',
                        'description' => 'Acme1',
                        'type' => 'Acme1',
                        'data' => 'Acme1',
                    ],
                    [
                        'name' => 'Acme2',
                        'profile' => 'Acme2',
                        'description' => 'Acme2',
                        'type' => 'Acme2',
                        'data' => 'Acme2',
                    ],
                ],
            ],
        ];
    }
}
