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

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use ONGR\SettingsBundle\Tests\Fixtures\Security\LoginTestHelper;
use ONGR\SettingsBundle\Tests\Functional\PreparePersonalData;
use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

/**
 * Tests for SettingsController.
 */
class PersonalSettingsControllerTest extends ElasticsearchTestCase
{
    /**
     * @var PreparePersonalData Elastic helper and index.
     */
    private $elastic;

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
        $this->client = new LoginTestHelper(self::createClient());
        $this->elastic = new PreparePersonalData();
    }

    /**
     * Test settings page ability to set values to cookie.
     */
    public function testSettingsAction()
    {
        $this->elastic->createIndexSetting();
        $this->elastic->insertSettingData();

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
        $form['settings[ongr_settings_profile_Acme1]']->untick();
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
            'ongr_settings_profile_Acme1' => false,
        ];
        $this->assertJsonStringEqualsJsonString(json_encode($expectedValue), $cookieValue);

        // Try to change value through change setting action.
        $client->request('get', '/settings/setting/change/' . base64_encode('foo_setting_1'));

        // Assert cookie values updated.
        $cookieValue = $client
            ->getCookieJar()
            ->get($client->getContainer()->getParameter('ongr_settings.settings.settings_cookie.name'))
            ->getValue();
        $expectedValue['foo_setting_1'] = false;
        $this->assertJsonStringEqualsJsonString(json_encode($expectedValue), $cookieValue);

        $this->elastic->cleanUp();
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

        // Assert successful redirect when not loged inn.
        $this->assertStringEndsWith(
            'login',
            $client->getRequest()->getUri(),
            'response must be a correct redirect'
        );
    }
}
