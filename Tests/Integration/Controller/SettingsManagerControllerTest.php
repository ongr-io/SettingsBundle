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

namespace ONGR\AdminBundle\Tests\Integration\Service;

use ONGR\AdminBundle\Model\SettingModel;
use ONGR\AdminBundle\Settings\Provider\SessionModelAwareProvider;
use ONGR\AdminBundle\Settings\SettingsContainer;
use ONGR\AdminBundle\Tests\Integration\BaseTest;
use Symfony\Bundle\FrameworkBundle\Client;

/**
 * Tests for SettingsManagerController
 */
class SettingsManagerControllerTest extends BaseTest
{
    /**
     * {@inheritDoc}
     */
    protected function getDocumentsData()
    {
        return [
            [
                '_id' => 'default_name0',
                'name' => 'name0',
                'description' => 'this should be updated',
                'domain' => 'default',
                'type' => SettingModel::TYPE_STRING,
                'data' => (object)['value' => 'test1']
            ]
        ];
    }

    /**
     * Data provider for testCopyAction
     *
     * @return array
     */
    public function copyActionData()
    {
        // #0 default value not set
        $out[] = [404, '/setting/name0/copy/from/newDomain'];

        // #1 default value is set
        $out[] = [200, '/setting/name0/copy/default/newDomain'];

        // #2 non-existent item passed
        $out[] = [404, '/setting/foo/copy/to/from/defalt'];

        return $out;
    }

    /**
     * Test for copyAction
     *
     * @param int $status
     * @param string $url
     *
     * @dataProvider copyActionData()
     */
    public function testCopyAction($status, $url)
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $this->assertEquals($status, $client->getResponse()->getStatusCode());
    }

    /**
     * Test for editAction()
     */
    public function testEditAction()
    {
        $client = static::createClient();
        $client->request('GET', '/setting/name0/edit');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * Data provider for testRemoveAction()
     *
     * @return array
     */
    public function removeActionData()
    {
        // #0 remove existing settings, check if default domain is set
        $out[] = ['/setting/name0/remove', 200];

        // #1 remove existing setting with domain set
        $out[] = ['/setting/name0/remove/default', 200];

        // #2 remove non-existing setting
        $out[] = ['/setting/non-existent/remove', 404];

        return $out;
    }

    /**
     * Test for removeAction()
     *
     * @dataProvider removeActionData()
     */
    public function testRemoveAction($url, $expectedStatusCode)
    {
        $client = static::createClient();
        $client->request('DELETE', $url);

        $this->assertEquals($expectedStatusCode, $client->getResponse()->getStatusCode());
    }

    /**
     * Test ngEditAction and ensure cached value is cleared
     */
    public function testCacheClearAfterModify()
    {
        $client = static::createClient();

        // Create setting
        $requestContent = json_encode(['setting' => ['data' => ['value' => 'foo']]]);
        $client->request('POST', '/setting/ng/setting_foo/edit/domain_foo', [], [], [], $requestContent);
        $response = $client->getResponse();
        $this->assertTrue($response->isOk());

        // Assert value
        $this->enableDomain($client);
        $this->assertSettingValue($client, 'foo');

        // Modify
        $requestContent = json_encode(['setting' => ['data' => ['value' => 'bar']]]);
        $client->request('POST', '/setting/ng/setting_foo/edit/domain_foo', [], [], [], $requestContent);
        $response = $client->getResponse();
        $this->assertTrue($response->isOk());

        // Assert modified value
        $this->enableDomain($client);
        $this->assertSettingValue($client, 'bar');
    }

    /**
     * Assert value has been set for custom domain_foo.
     *
     * @param Client $client
     * @param string $expectedValue
     */
    protected function assertSettingValue(Client $client, $expectedValue)
    {
        $settingsContainer = $client->getContainer()->get('ongr_admin.settings_container');
        $value = $settingsContainer->get('setting_foo');
        $this->assertSame($expectedValue, $value);
    }

    /**
     * Add domain_foo so that the setting can be read.
     *
     * @param Client $client
     * @return SettingsContainer
     */
    protected function enableDomain(Client $client)
    {
        $container = $client->getContainer();
        /** @var SettingsContainer $settingsContainer */
        $settingsContainer = $container->get('ongr_admin.settings_container');
        $settingsContainer->setDomains(['domain_foo']);

        /** @var SessionModelAwareProvider $provider */
        $provider = $container->get('ongr_admin.dummy_domain_provider');
        $settingsContainer->addProvider($provider);
    }
}
