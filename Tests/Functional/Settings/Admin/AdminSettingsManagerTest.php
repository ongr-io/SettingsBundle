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

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use ONGR\AdminBundle\Settings\Admin\AdminSettingsManager;

class AdminSettingsManagerTest extends WebTestCase
{
    /**
     * Get manager.
     *
     * @return AdminSettingsManager
     */
    public function getServiceSettingsManager()
    {
        return self::createClient()->getContainer()->get('ongr_admin.settings.admin_settings_manager');
    }

    /**
     * Create mock array.
     *
     * @return array
     */
    public function getMockArray()
    {
        return [ 'Key 1' => 'Test 1' ];
    }

    /**
     * Create mock Admin settings manager with authorization enabled.
     *
     * @return Container
     */
    public function getManagerWithSecurityMock()
    {
        $scm = $this->getMock('SecurityContextInterface', ['isGranted']);
        $scm->expects($this->once())
            ->method('isGranted')
            ->will($this->returnValue(true));

        $container = new ContainerBuilder();
        $container->register('test.sm', 'ONGR\AdminBundle\Settings\Admin\AdminSettingsManager')
            ->addArgument($scm)
            ->addArgument('b');

        return $container->get('test.sm');
    }

    /**
     * Tests are settings inserted and loaded from cookies.
     */
    public function testSetSettingsFromCookie()
    {
        $manager = $this->getServiceSettingsManager();
        $manager->setSettingsFromCookie($this->getMockArray());

        $this->assertEquals($manager->getSettings(), $this->getMockArray());
    }

    /**
     * Tests are settings added (merged) and loaded from cookies.
     */
    public function testAddSettingsFromCookie()
    {
        $manager = $this->getServiceSettingsManager();

        // Add first element so initial array for test case wont be empty.
        $manager->setSettingsFromCookie($this->getMockArray());

        // Add new element for merge inside a method.
        $manager->addSettingsFromCookie([ 'Key 2' => 'Test 2' ]);

        // Assert.
        $this->assertEquals(
            $manager->getSettings(),
            [
                'Key 1' => 'Test 1',
                'Key 2' => 'Test 2',
            ]
        );
    }

    /**
     * Tests are settings inserted and loaded from form.
     */
    public function testSetSettingsFromForm()
    {
        $manager = $this->getServiceSettingsManager();
        $manager->setSettingsFromForm($this->getMockArray());

        $this->assertEquals($manager->getSettings(), $this->getMockArray());
    }

    /**
     * Tests is single setting disabled. Authentication check is on but fails.
     */
    public function testGetSettingEnabledIsEnabledFailAuth()
    {
        $manager = $this->getServiceSettingsManager();
        $manager->setSettingsFromCookie($this->getMockArray());
        $setting = $manager->getSettingEnabled('Key 1', true);

        $this->assertEquals($setting, false);
    }

    /**
     * Tests is single setting enabled. Authentication check is off.
     */
    public function testGetSettingEnabledIsEnabledNotAuth()
    {
        $manager = $this->getServiceSettingsManager();
        $manager->setSettingsFromCookie($this->getMockArray());
        $setting = $manager->getSettingEnabled('Key 1', false);

        $this->assertEquals($setting, 'Test 1');
    }

    /**
     * Tests is single setting disabled. Authentication check is off.
     */
    public function testGetSettingEnabledIsNotEnabledNotAuth()
    {
        $manager = $this->getServiceSettingsManager();
        $manager->setSettingsFromCookie($this->getMockArray());
        $setting = $manager->getSettingEnabled('Key 2', false);

        $this->assertEquals($setting, false);
    }

    /**
     * Tests is single setting enabled. Authentication check is on.
     */
    public function testGetSettingEnabledIsEnabledAuth()
    {
        /** @var AdminSettingsManager $manager */
        $manager = $this->getManagerWithSecurityMock();
        $manager->setSettingsFromCookie($this->getMockArray());
        $setting = $manager->getSettingEnabled('Key 1', true);

        $this->assertEquals($setting, 'Test 1');
    }

    /**
     * Tests is single setting disabled. Authentication check is on.
     */
    public function testGetSettingEnabledIsNotEnabledAuth()
    {
        // Test case, that setting is enabled, for authorized.
        /** @var AdminSettingsManager $manager */
        $manager = $this->getManagerWithSecurityMock();
        $manager->setSettingsFromCookie($this->getMockArray());
        $setting = $manager->getSettingEnabled('Key 2');

        $this->assertEquals($setting, false);
    }

    /**
     * Tests method isAuthenticated. False.
     */
    public function testIsAuthenticatedFalse()
    {
        $manager = $this->getServiceSettingsManager();

        $this->assertEquals($manager->isAuthenticated(), false);
    }

    /**
     * Tests method isAuthenticated. True.
     */
    public function testIsAuthenticatedTrue()
    {
        $this->assertEquals($this->getManagerWithSecurityMock()->isAuthenticated(), true);
    }

    /**
     * Tests method getSettingsMap.
     */
    public function testGetSettingsMap()
    {
    }

    /**
     * Tests method getCategoryMap.
     */
    public function testGetCategoryMap()
    {
        $category_map_expected = [
            'category_1' => [
                'name' => 'Category 1',
                'description' => 'cat_desc_1',
            ],
            'category_2' => [
                'name' => 'Category 2',
            ],
            'ongr_admin_settings' => [
                'name' => 'ONGR admin',
                'description' => 'Special settings for ONGR admin',
            ],
            'ongr_admin_profiles' => [
                'name' => 'ONGR admin profiles',
                'description' => 'Profiles for profile settings',
            ],
        ];

        $manager = $this->getServiceSettingsManager();

        $this->assertEquals($manager->getCategoryMap(), $category_map_expected);
    }
}
