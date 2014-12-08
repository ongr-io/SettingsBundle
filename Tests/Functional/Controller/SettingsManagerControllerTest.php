<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Functional\Controller;

use ONGR\AdminBundle\Document\Setting;
use ONGR\AdminBundle\Settings\Common\Provider\ManagerAwareSettingProvider;
use ONGR\AdminBundle\Settings\Common\SettingsContainer;
use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

/**
 * Tests for SettingsManagerController.
 */
class SettingsManagerControllerTest extends ElasticsearchTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $setting = new Setting();
        $setting->setId('default_name0');
        $setting->name = 'name0';
        $setting->description = 'this should be updated';
        $setting->profile = 'default';
        $setting->type = Setting::TYPE_STRING;
        $setting->data = (object)['value' => 'test1'];

        $manager = $this->getManager();
        $manager->persist($setting);
        $manager->commit();
        $manager->flush();
    }

    /**
     * Data provider for testCopyAction.
     *
     * @return array
     */
    public function copyActionData()
    {
        // Case #0 non existing profile, existing item passed.
        $out[] = [500, '/admin/setting/name0/copy/foo/newProfile'];

        // Case #1 existing profile set, existing item passed.
        $out[] = [200, '/admin/setting/name0/copy/default/newProfile'];

        // Case #2 non-existent profile and item passed.
        $out[] = [500, '/admin/setting/foo/copy/foo/newProfile'];

        // Case #3 existent profile, non-existing item passed.
        $out[] = [500, '/admin/setting/foo/copy/default/newProfile'];

        return $out;
    }

    /**
     * Test for copyAction.
     *
     * @param int    $status
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
     * Test for editAction().
     */
    public function testEditAction()
    {
        $client = static::createClient();
        $client->request('GET', '/admin/setting/name0/edit');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * Data provider for testRemoveAction().
     *
     * @return array
     */
    public function removeActionData()
    {
        // Case #0 remove existing settings, check if default domain is set.
        $out[] = ['/admin/setting/name0/remove', 200];

        // Case #1 remove existing setting with domain set.
        $out[] = ['/admin/setting/name0/remove/default', 200];

        // Case #2 remove non-existing setting.
        $out[] = ['/admin/setting/non-existent/remove', 500];

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
        $client = static::createClient();
        $client->request('DELETE', $url);

        $this->assertEquals($expectedStatusCode, $client->getResponse()->getStatusCode());
    }

    /**
     * Test ngEditAction and ensure cached value is cleared.
     */
    public function testCacheClearAfterModify()
    {
        $client = static::createClient();

        // Create setting.
        $requestContent = json_encode(['setting' => ['data' => ['value' => 'foo']]]);
        $client->request('POST', '/admin/setting/ng/setting_foo/edit/domain_foo', [], [], [], $requestContent);
        $response = $client->getResponse();
        $this->assertTrue($response->isOk());

        // Assert value.
        $this->enableDomain($client);
        $this->assertSettingValue($client, 'foo');

        // Modify.
        $requestContent = json_encode(['setting' => ['data' => ['value' => 'bar']]]);
        $client->request('POST', '/admin/setting/ng/setting_foo/edit/domain_foo', [], [], [], $requestContent);
        $response = $client->getResponse();
        $this->assertTrue($response->isOk());

        // Assert modified value.
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
     */
    protected function enableDomain(Client $client)
    {
        $container = $client->getContainer();
        /** @var SettingsContainer $settingsContainer */
        $settingsContainer = $container->get('ongr_admin.settings_container');
        $settingsContainer->setProfiles(['domain_foo']);

        /** @var ManagerAwareSettingProvider $provider */
        $provider = $container->get('ongr_admin.dummy_profile_provider');
        $settingsContainer->addProvider($provider);
    }
}
