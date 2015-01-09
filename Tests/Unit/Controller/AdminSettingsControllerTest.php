<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Unit\Controller;

use ONGR\AdminBundle\Controller\AdminSettingsController;
use ONGR\AdminBundle\Settings\Admin\SettingsStructure;
use ONGR\AdminBundle\Settings\SettingsCookieService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;

class AdminSettingsControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test changeSettingAction.
     */
    public function testChangeSettingAction()
    {
        $container = new ContainerBuilder();

        $container->set('ongr_admin.settings.admin_settings_manager', $this->getManagerMock());
        $container->set('ongr_admin.settings.settings_cookie', $this->getCookiesServiceMock());

        $controller = new AdminSettingsController();
        $controller->setContainer($container);
        $resp = $controller->changeSettingAction(new Request(), base64_encode('encoded_testA'));

        // TODO: Assertions.
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function getManagerMock()
    {
        $securityContextInterfaceMock = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $settingsStructure = $this->getMock(
            'ONGR\AdminBundle\Settings\Admin\SettingsStructure',
            [],
            [
                [
                    ['$settingsParameter'],
                ],
                [
                    ['$categoriesParameter'],
                ],
            ]
        );

        $mock = $this->getMock(
            'ONGR\AdminBundle\Settings\Admin\AdminSettingsManager',
            [],
            [$securityContextInterfaceMock, $settingsStructure]
        );
        $mock->method('getSettingsMap')->willReturn(
            [
                'encoded_testA' => [
                    'name' => 'test_name',
                    'category' => 'test_category',
                    'cookie' => 'ongr_admin.settings.settings_cookie',
                ],
                'encoded_test' => [
                    'name' => 'test_name',
                    'category' => 'test_category',
                    'cookie' => 'ongr_admin.settings.settings_cookie',
                ],
            ]
        );
        $mock->method('getSettings')->willReturn(['encoded_test' => 'encoded_test', 'encoded_test' => 'encoded_testA']);

        return $mock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function getCookiesServiceMock()
    {
        $mock = $this->getMock('ONGR\AdminBundle\Settings\SettingsCookieService', ['setValue']);

        return $mock;
    }
}
