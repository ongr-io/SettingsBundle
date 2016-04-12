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
use ONGR\SettingsBundle\Settings\General\Provider\ManagerAwareSettingProvider;
use ONGR\SettingsBundle\Settings\General\SettingsContainer;
use ONGR\SettingsBundle\Document\Profile;
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

    public function getDataArray()
    {
        return ['default' => []];
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->client = static::createClient();

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
        $out[] = [302, '/settings/setting/test_setting/copy/test', 'non_existant_profile', false];

        // Case #1 existing profile set, existing item passed.
        $out[] = [302, '/settings/setting/test_setting/copy/test', 'new_profile', true];

        // Case #2 non-existent item passed.
        $out[] = [500, '/settings/setting/non_existant_setting/copy/test', 'new_profile', false];

        // Case #3 existent item, non-existing old profile passed.
        $out[] = [500, '/settings/setting/test_setting/copy/non_existant_profile', 'new_profile', false];

        return $out;
    }

    /**
     * Creates a profile
     * @param string $profile
     */
    private function createProfile($profile)
    {
        $requestParameters = [
            'profileName' => $profile,
            'profileDescription' => 'test profile'
        ];
        $this->client->request('POST', '/settings/create/profile', $requestParameters);
    }

    /**
     * Create setting.
     * @param string $name
     * @param string $profile
     */
    private function createSetting($name, $profile)
    {
        $requestParameters = [
            'settingName' => $name,
            'settingProfiles' => [$profile],
            'settingDescription' => 'description0',
            'settingType' => 'string',
            'setting-default' => 'test value'
        ];
        $this->client->request('POST', '/settings/setting/set/', $requestParameters);
    }

    /**
     * Data provider for testCreateAction().
     *
     * @return array
     */
    public function createActionData()
    {
        // case #0 create setting with valid request content
        $out[] = [
            [
                'settingName' => 'data',
                'settingProfiles' => ['test'],
                'settingDescription' => 'description0',
                'settingType' => 'string',
                'setting-default' => 'test value'
            ],
            302,
            true
        ];

        // case #1 create setting with blank request parameters
        $out[] = [[], 302, false];

        // case #3 create setting with no profile
        $out[] = [
            [
                'settingName' => 'data',
                'settingDescription' => 'description0',
                'settingType' => 'string',
                'setting-default' => 'test value'
            ],
            302,
            false
        ];

        // case #3 create array setting
        $out[] = [
            [
                'settingName' => 'data',
                'settingProfiles' => ['test'],
                'settingDescription' => 'description0',
                'settingType' => 'array',
                'setting-array_0' => 1,
                'setting-array_1' => 2,
            ],
            302,
            true
        ];

        // case #4 create bool setting
        $out[] = [
            [
                'settingName' => 'data',
                'settingProfiles' => ['test'],
                'settingDescription' => 'description0',
                'settingType' => 'bool',
                'setting-boolean' => 'true'
            ],
            302,
            true
        ];

        // case #5 create setting with no value
        $out[] = [
            [
                'settingName' => 'data',
                'settingProfiles' => ['test'],
                'settingDescription' => 'description0',
                'settingType' => 'string',
            ],
            302,
            false
        ];

        return $out;
    }

    /**
     * Tests profile creation
     */
    public function testCreateProfile()
    {
        $requestParameters = [
            'profileName' => 'test',
            'profileDescription' => 'test'
        ];
        $this->client->request('POST', '/settings/create/profile', $requestParameters);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test for createAction().
     *
     * @param array   $requestParameters
     * @param int     $statusCode
     * @param bool    $created
     *
     * @dataProvider createActionData()
     */
    public function testCreateAction($requestParameters, $statusCode, $created)
    {
        $manager = $this->getManager();
        $profile = new Profile();
        $profile->setName('test');
        $manager->persist($profile);
        $manager->commit();

        $this->client->request('POST', '/settings/setting/set/', $requestParameters);
        $this->assertEquals($statusCode, $this->client->getResponse()->getStatusCode());
        $settings = $manager->find('ONGRSettingsBundle:Setting', 'test_data');
        if ($created) {
            $this->assertNotNull($settings);
        } else {
            $this->assertNull($settings);
        }
    }

    /**
     * Test for copyAction.
     *
     * @param int    $status
     * @param string $url
     * @param string $new_profile
     * @param bool   $created
     *
     * @dataProvider copyActionData()
     */
    public function testCopyAction($status, $url, $new_profile, $created)
    {
        $this->createProfile('test');
        $this->createProfile('new_profile');
        $this->createSetting('test_setting', 'test');
        $this->client->request('POST', $url, ['settingProfiles' => [$new_profile]]);
        $this->assertEquals($status, $this->client->getResponse()->getStatusCode());

        $settings = $this->getManager()->find('ONGRSettingsBundle:Setting', $new_profile.'_test_setting');
        if ($created) {
            $this->assertNotNull($settings);
        } else {
            $this->assertNull($settings);
        }
    }

    /**
     * Test for editAction().
     */
    public function testEditAction()
    {
        $this->createProfile('test');
        $this->createSetting('test_setting', 'test');
        $this->client->request('GET', '/settings/setting/test_setting/edit/test');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Data provider for testRemoveAction().
     *
     * @return array
     */
    public function removeActionData()
    {
        // Case #0 remove existing setting with domain set.
        $out[] = ['/settings/setting/test_setting/remove/test', 302];

        // Case #1 remove non-existing setting.
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
        $this->createProfile('test');
        $this->createSetting('test_setting', 'test');
        $this->client->request('DELETE', $url);
        $this->assertEquals($expectedStatusCode, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test EditAction and ensure cached value is cleared.
     */
    public function testCacheClearAfterModify()
    {
        $client = $this->client;
        $this->createProfile('domain_foo');
        // Create setting.
        $requestContent = [
            'settingName' => 'setting_foo',
            'settingType' => 'string',
            'settingProfiles' => ['domain_foo'],
            'setting-default' => 'foo'
        ];
        $client->request('POST', '/settings/setting/update/', $requestContent);

        // Assert value.
        $this->enableDomain($client);
        $this->assertSettingValue($client, 'foo');

        // Modify.
        $requestContent['setting-default'] = 'bar';
        $client->request('POST', '/settings/setting/update/', $requestContent);

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
