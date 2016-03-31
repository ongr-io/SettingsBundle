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
use ONGR\SettingsBundle\Settings\Common\Provider\ManagerAwareSettingProvider;
use ONGR\SettingsBundle\Settings\Common\SettingsContainer;
use ONGR\SettingsBundle\Tests\Fixtures\Security\LoginTestHelper;
use Symfony\Bundle\FrameworkBundle\Client;

/**
 * Tests for SettingsManagerController.
 */
class SettingsManagerControllerTest extends AbstractElasticsearchTestCase
{
    /**
     * @var Client.
     */
    private $client;

    /**
     * @var LoginTestHelper.
     */
    private $loginHelper;

    /**
     * @var Container
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->loginHelper = new LoginTestHelper(static::createClient());
        $this->client = $this->loginHelper->loginAction();

        $this->container = $this->client->getContainer();
    }

    /**
     * Data provider for testCopyAction.
     *
     * @return array
     */
    public function copyActionData()
    {
        // Case #0 non existing profile, existing item passed.
        $out[] = [500, '/settings/setting/name0/copy/foo/newProfile'];

        // Case #1 existing profile set, existing item passed.
        $out[] = [200, '/settings/setting/name0/copy/default/newProfile'];

        // Case #2 non-existent profile and item passed.
        $out[] = [500, '/settings/setting/foo/copy/foo/newProfile'];

        // Case #3 existent profile, non-existing item passed.
        $out[] = [500, '/settings/setting/foo/copy/default/newProfile'];

        return $out;
    }

    /**
     * Create setting.
     */
    public function createSetting()
    {
        $requestContent = json_encode(['setting' => ['data' => ['value' => 'name0']]]);
        $this->client->request('POST', '/settings/setting/ng/name0/edit/default', [], [], [], $requestContent);
    }

    /**
     * Data provider for testCreateAction().
     *
     * @return array
     */
    public function createActionData()
    {
        // case #0 create setting with valid request content
        $out[] = [json_encode(['setting' => ['data' => ['value' => 'foo'], 'description' => 'description0']]), 200];

        // case #1 create setting with blank request content
        $out[] = [json_encode([]), 400];

        // case #2 create setting with no request content
        $out[] = [null, 400];

        return $out;
    }

    /**
     * Test for createAction().
     *
     * @param string $requestContent
     * @param int    $statusCode
     *
     * @dataProvider createActionData()
     */
    public function testCreateAction($requestContent, $statusCode)
    {
        $this->client->request('POST', '/settings/setting/ng/setting_foo/edit/domain_foo', [], [], [], $requestContent);
        $this->assertEquals($statusCode, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test for copyAction.
     *
     * @param int    $status
     * @param string $url
     *
     * @dataProvider copyActionData()
     */
    public function testCopyActionLogedIn($status, $url)
    {
        $this->createSetting();
        $this->client->request('GET', $url);
        $this->assertEquals($status, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test for copyAction after logged Out.
     *
     * @param int    $status
     * @param string $url
     *
     * @dataProvider copyActionData()
     */
    public function testCopyActionLogedOut($status, $url)
    {
        $this->createSetting();
        $this->client = $this->loginHelper->logoutAction($this->client);
        $this->client->request('GET', $url);
        $this->assertSame('/settings/login', $this->client->getRequest()->getRequestUri());
    }

    /**
     * Test for editAction().
     */
    public function testEditAction()
    {
        $this->createSetting();
        $this->client->request('GET', '/settings/setting/name0/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Data provider for testRemoveAction().
     *
     * @return array
     */
    public function removeActionData()
    {
        // Case #0 remove existing settings, check if default domain is set.
        $out[] = ['/settings/setting/name0/remove', 200];

        // Case #1 remove existing setting with domain set.
        $out[] = ['/settings/setting/name0/remove/default', 200];

        // Case #2 remove non-existing setting.
        $out[] = ['/settings/setting/non-existent/remove', 500];

        return $out;
    }

    /**
     * Test for removeAction().
     *
     * @param string $url
     * @param string $expectedStatusCode
     *
     * @dataProvider removeActionData()
     */
    public function testRemoveAction($url, $expectedStatusCode)
    {
        $this->createSetting();
        $this->client->request('DELETE', $url);
        $this->assertEquals($expectedStatusCode, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test ngEditAction and ensure cached value is cleared.
     */
    public function testCacheClearAfterModify()
    {
        $client = $this->client;

        // Create setting.
        $requestContent = json_encode(['setting' => ['data' => ['value' => 'foo']]]);
        $client->request('POST', '/settings/setting/ng/setting_foo/edit/domain_foo', [], [], [], $requestContent);
        $response = $client->getResponse();
        $this->assertTrue($response->isOk());

        // Assert value.
        $this->enableDomain($client);
        $this->assertSettingValue($client, 'foo');

        // Modify.
        $requestContent = json_encode(['setting' => ['data' => ['value' => 'bar']]]);
        $client->request('POST', '/settings/setting/ng/setting_foo/edit/domain_foo', [], [], [], $requestContent);
        $response = $client->getResponse();
        $this->assertTrue($response->isOk());

        // Assert modified value.
        $this->enableDomain($client);
        $this->assertSettingValue($client, 'bar');
    }

    /**
     * Assert value has been set.
     *
     * @param Client $client
     * @param string $expectedValue
     */
    protected function assertSettingValue(Client $client, $expectedValue)
    {
        $settingsContainer = $client->getContainer()->get('ongr_settings.settings_container');
        $value = $settingsContainer->get('setting_foo');
        $this->assertSame($expectedValue, $value);
    }

    /**
     * Add domain_foo so that the setting can be read.
     *
     * @param Client $client
     */
    protected function enableDomain(Client $client)
    {
        $container = $client->getContainer();
        /** @var SettingsContainer $settingsContainer */
        $settingsContainer = $container->get('ongr_settings.settings_container');
        $settingsContainer->setProfiles(['domain_foo']);

        /** @var ManagerAwareSettingProvider $provider */
        $provider = $container->get('ongr_settings.dummy_profile_provider');
        $settingsContainer->addProvider($provider);
    }
}
