<?php

/*
 *************************************************************************
 * NFQ eXtremes CONFIDENTIAL
 * [2013] - [2014] NFQ eXtremes UAB
 * All Rights Reserved.
 *************************************************************************
 * NOTICE:
 * All information contained herein is, and remains the property of NFQ eXtremes UAB.
 * Dissemination of this information or reproduction of this material is strictly forbidden
 * unless prior written permission is obtained from NFQ eXtremes UAB.
 *************************************************************************
 */

namespace ONGR\AdminBundle\Tests\Integration\Settings;

use ONGR\AdminBundle\Model\SettingModel;
use ONGR\AdminBundle\Tests\Integration\BaseTest;
use ONGR\UtilsBundle\Tests\Integration\CookieTestHelper;

/**
 * Test integration with power-user feature in fox-utils
 */
class PowerUserSettingsTest extends BaseTest
{
    /**
     * {@inheritDoc}
     */
    protected function getDocumentsData()
    {
        return [
            [
                'name' => 'foo_default',
                'description' => 'Description',
                'domain' => 'default',
                'type' => SettingModel::TYPE_ARRAY,
                'data' => ['customData' => 'testData'],
            ],
            [
                'name' => 'foo',
                'description' => 'Description',
                'domain' => 'domain_foo.com',
                'type' => SettingModel::TYPE_ARRAY,
                'data' => ['customData' => 'testData'],
            ],
            [
                'name' => 'bar',
                'description' => 'Description',
                'domain' => 'domain_bar.com',
                'type' => SettingModel::TYPE_ARRAY,
                'data' => ['customData' => 'testData']
            ],
        ];
    }

    /**
     * When user is logged in, he should see fox-admin settings and domain checkboxes
     */
    public function testSettingsDisplayed()
    {
        // Set authentication cookie
        $client = self::createClient();
        CookieTestHelper::setAuthenticationCookie($client);

        // Retrieve content
        $crawler = $client->request('GET', '/power-user/settings');
        // var_dump($client->getResponse()->getContent()); // Helps debugging output

        // Asserts
        $settingsDescription = $crawler->filter('#settings_ongr_admin_live_settings');
        $this->assertCount(1, $settingsDescription, 'Live settings setting must exist');

        $domain = $crawler->filter('#settings_ongr_admin_domain_default');
        $this->assertCount(1, $domain, 'Domain default checkbox must exist');

        $domain = $crawler->filter('#settings_ongr_admin_domain_domain_foo-2e-com');
        $this->assertCount(1, $domain, 'Domain foo checkbox must exist');

        $categories = $crawler->filter('.category');
        $this->assertCount(2, $categories);
    }

    /**
     * When user is logged in, and he selects profile, then settings container must receive that choice
     */
    public function testSettingsSelected()
    {
        // Set authentication cookie
        $client = self::createClient();
        CookieTestHelper::setAuthenticationCookie($client);

        // Retrieve content
        $crawler = $client->request('GET', '/power-user/settings');

        // Submit domain selection
        $buttonNode = $crawler->selectButton('settings_submit');
        $form = $buttonNode->form();
        /** @noinspection PhpUndefinedMethodInspection */
        $form['settings[ongr_admin_domain_domain_foo-2e-com]']->tick();
        $client->submit($form);

        // Load any url and check that user selected domains are loaded
        $client->request('GET', '/setting/name0/edit');
        $settingsContainer = $client->getContainer()->get('ongr_admin.settings_container');
        $selectedDomains = $settingsContainer->getDomains();
        $this->assertEquals(['default', 'domain_foo.com'], $selectedDomains);
    }
}
