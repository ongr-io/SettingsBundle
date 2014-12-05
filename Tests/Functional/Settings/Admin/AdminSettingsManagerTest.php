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
     * Create mock array.
     *
     * @return array
     */
    public function getMockArray()
    {
        return [ 'Key 1' => 'Test 1' ];
    }

    /**
     * Data provider for testGeneralGet.
     *
     * @return array
     */
    public function getGeneralTestData()
    {
        $out = [];

        // Case #0 Tests are settings inserted and loaded from cookies.
        $out[] = [
            'data' => $this->getMockArray(),
            'add' => [],
            'form' => [],
            'expected' => $this->getMockArray(),
        ];

        // Case #1 Tests are settings added (merged) and loaded from cookies.
        $out[] = [
            'data' => $this->getMockArray(),
            'add' => [ 'Key 2' => 'Test 2' ],
            'form' => [],
            'expected' => $this->getMockArray() + [ 'Key 2' => 'Test 2' ],
        ];

        // Case #2 Tests are settings inserted and loaded from form.
        $out[] = [
            'data' => [],
            'add' => [],
            'form' => $this->getMockArray(),
            'expected' => $this->getMockArray(),
        ];

        return $out;
    }

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
     * Create mock Admin settings manager with authorization enabled.
     *
     * @return ContainerBuilder
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
     * Test Settings manager.
     *
     * @param array $data
     * @param array $add
     * @param array $form
     * @param array $expected
     *
     * @dataProvider getGeneralTestData
     */
    public function testGeneralGet($data, $add, $form, $expected)
    {
        $manager = $this->getServiceSettingsManager();

        if (!empty($data)) {
            $manager->setSettingsFromCookie($data);
        }

        if (!empty($add)) {
            $manager->addSettingsFromCookie($add);
        }

        if (!empty($form)) {
            $manager->setSettingsFromForm($form);
        }

        $this->assertEquals($manager->getSettings(), $expected);
    }

    /**
     * Data provider for testValueGet.
     *
     * @return array
     */
    public function getValueTestData()
    {
        $out = [];

        // Case #0 Tests is single setting disabled. Authentication check is on but fails.
        $out[] = [
            'security' => false,
            'data' => $this->getMockArray(),
            'key' => 'Key 1',
            'authorize' => true,
            'expected' => false,
        ];

        // Case #1 Tests is single setting enabled. Authentication check is off.
        $out[] = [
            'security' => false,
            'data' => $this->getMockArray(),
            'key' => 'Key 1',
            'authorize' => false,
            'expected' => 'Test 1',
        ];

        // Case #2 Tests is single setting disabled. Authentication check is off.
        $out[] = [
            'security' => false,
            'data' => $this->getMockArray(),
            'key' => 'Key 2',
            'authorize' => false,
            'expected' => false,
        ];

        // Case #3 Tests is single setting enabled. Authentication check is on.
        $out[] = [
            'security' => true,
            'data' => $this->getMockArray(),
            'key' => 'Key 1',
            'authorize' => true,
            'expected' => 'Test 1',
        ];

        // Case #4 Tests is single setting disabled. Authentication check is on.
        $out[] = [
            'security' => true,
            'data' => $this->getMockArray(),
            'key' => 'Key 2',
            'authorize' => true,
            'expected' => false,
        ];

        return $out;
    }

    /**
     * Test Settings manager.
     *
     * @param bool   $security
     * @param array  $data
     * @param string $key
     * @param bool   $authorize
     * @param mixed  $expected
     *
     * @dataProvider getValueTestData
     */
    public function testValueGet($security, $data, $key, $authorize, $expected)
    {
        /** @var AdminSettingsManager $manager */
        if ($security == true) {
            $manager = $this->getManagerWithSecurityMock();
        } else {
            $manager = $this->getServiceSettingsManager();
        }

        $manager->setSettingsFromCookie($data);

        $this->assertEquals($manager->getSettingEnabled($key, $authorize), $expected);
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
