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

namespace Fox\AdminBundle\Tests\Integration\Twig;

use Fox\AdminBundle\Exception\SettingNotFoundException;
use Fox\AdminBundle\Tests\Integration\BaseTest;
use Fox\AdminBundle\Twig\AdminExtension;
use Fox\UtilsBundle\Settings\UserSettingsManager;

/**
 * Class used to test AdminExtension
 */
class AdminExtensionTest extends BaseTest
{
    /**
     * Tests if extension is loaded correctly
     */
    public function testGetExtension()
    {
        $container = self::createClient()->getContainer();
        /** @var AdminExtension $extension */
        $extension = $container->get('fox_admin.twig.admin_extension');
        $this->assertInstanceOf(
            'Fox\AdminBundle\Twig\AdminExtension',
            $extension,
            'extension has wrong instance.'
        );
        $this->assertNotEmpty($extension->getFunctions(), 'extension does not have functions defined.');
    }

    /**
     * Gets a UserSettingsManager mock
     *
     * @param bool $authenticated
     *
     * @return UserSettingsManager
     */
    protected function getSettingsManagerMock($authenticated)
    {
        $settingsManager = $this->getMockBuilder('Fox\UtilsBundle\Settings\UserSettingsManager')
            ->disableOriginalConstructor()
            ->setMethods(['isAuthenticated'])
            ->getMock();

        $settingsManager->expects($this->once())->method('isAuthenticated')->willReturn($authenticated);

        return $settingsManager;
    }

    /**
     * Returns mock of sessionless token
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getTokenMock()
    {
        return $this->getMockBuilder('Fox\\UtilsBundle\\Security\\Authentication\\Token\\SessionlessToken')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Data provider for testShowSetting
     *
     * @return array[]
     */
    public function showSettingData()
    {
        //#0 not authenticated
        $expectedOutput = '';
        $out[] = [$expectedOutput, 'test', false];

        //#1 default type (string)
        $expectedOutput = <<<HEREDOC
<a href="http://localhost/setting/test/edit?type=string" class="btn btn-default pull-right" title="Edit test">
    <span class="glyphicon glyphicon-wrench"></span>
</a>
HEREDOC;
        $out[] = [$expectedOutput, 'test', true];

        //#2 custom type (array)
        $expectedOutput = <<<HEREDOC
<a href="http://localhost/setting/test/edit?type=array" class="btn btn-default pull-right" title="Edit test">
    <span class="glyphicon glyphicon-wrench"></span>
</a>
HEREDOC;
        $out[] = [$expectedOutput, 'test', true, 'array'];

        return $out;
    }

    /**
     * Test getPriceList()
     *
     * @param string $expectedOutput
     * @param string $type
     * @param bool $isAuthenticated
     * @param string $settingName
     *
     * @dataProvider showSettingData
     */
    public function testShowSetting($expectedOutput, $settingName, $isAuthenticated, $type = null)
    {
        $container = self::createClient()->getContainer();
        $securityContext = $container->get('fox_utils.authentication.sessionless_security_context');
        $securityContext->setToken($this->getTokenMock());
        $settingsManager = $container->get('fox_utils.settings.user_settings_manager');
        $settingsManager->setSettingsFromForm(['fox_admin_live_settings' => true]);

        /** @var \Twig_Environment $twig */
        $twig = $container->get('twig');
        $extension = new AdminExtension($this->getSettingsManagerMock($isAuthenticated));

        if (empty($type)) {
            $result = $extension->showSetting($twig, $settingName);
        } else {
            $result = $extension->showSetting($twig, $settingName, $type);
        }

        $this->assertEquals(trim($expectedOutput), trim($result));
    }

    /**
     * Test for getAdminSetting()
     */
    public function testGetAdminSetting()
    {
        $expectedValue = 'foo-bar';

        $settingContainer = $this->getMock('Fox\AdminBundle\Settings\SettingsContainerInterface');
        $settingContainer->expects($this->once())->method('get')->with('test')->willReturn($expectedValue);

        $extension = new AdminExtension(null);
        $extension->setSettingsContainer($settingContainer);

        $this->assertEquals($expectedValue, $extension->getAdminSetting('test'));
    }

    /**
     * Test for getAdminSetting() in case setting was not found
     */
    public function testGetAdminSettingException()
    {
        $settingContainer = $this->getMock('Fox\AdminBundle\Settings\SettingsContainerInterface');
        $settingContainer
            ->expects($this->once())
            ->method('get')
            ->with('test')
            ->willThrowException(new SettingNotFoundException());

        $extension = new AdminExtension(null);
        $extension->setSettingsContainer($settingContainer);

        $this->assertNull($extension->getAdminSetting('test'));
    }
}
