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
use Symfony\Component\HttpFoundation\Response;

class AdminSettingsControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Data provider for testSetSettingAction.
     *
     * @return array
     */
    public function getSetSettingActionData()
    {
        $out = [];

        // Case #0 OK.
        $out[0] = [
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
            ],
            true,
        ];

        // Case #0 Should fail.
        $out[1] = [
            [
                'encoded_test' => [
                    'name' => 'test_name',
                    'category' => 'test_category',
                    'cookie' => 'ongr_admin.settings.settings_cookie',
                ],
            ],
            false,
        ];

        return $out;
    }

    /**
     * Test changeSettingAction.
     *
     * @param array $testData
     * @param bool  $expected
     *
     * @dataProvider getSetSettingActionData
     */
    public function testChangeSettingAction($testData, $expected)
    {
        $container = new ContainerBuilder();

        $container->set('ongr_admin.settings.admin_settings_manager', $this->getManagerMock($testData));
        $container->set('ongr_admin.settings.settings_cookie', $this->getCookiesServiceMock());

        $controller = new AdminSettingsController();
        $controller->setContainer($container);
        $response = $controller->changeSettingAction(new Request(), base64_encode('encoded_testA'));

        if ($expected) {
            $statusCode = Response::HTTP_OK;
        } else {
            $statusCode = Response::HTTP_FORBIDDEN;
        }

        $this->assertEquals(
            $statusCode,
            $response->getStatusCode()
        );
    }

    /**
     * @param array $testData
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function getManagerMock($testData)
    {
        $securityContextInterfaceMock = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $settingsStructure = $this->getMock(
            'ONGR\AdminBundle\Settings\Admin\SettingsStructure',
            [],
            [
                [
                    ['settingsParameterMock'],
                ],
                [
                    ['categoriesParameterMock'],
                ],
            ]
        );

        $mock = $this->getMock(
            'ONGR\AdminBundle\Settings\Admin\AdminSettingsManager',
            [],
            [$securityContextInterfaceMock, $settingsStructure]
        );
        $mock->method('getSettingsMap')->willReturn($testData);
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
