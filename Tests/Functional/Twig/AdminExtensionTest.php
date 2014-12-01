<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Integration\Twig;

use ONGR\AdminBundle\Exception\SettingNotFoundException;
use ONGR\AdminBundle\Tests\Integration\BaseTest;
use ONGR\AdminBundle\Twig\AdminExtension;
use ONGR\AdminBundle\Settings\UserSettingsManager;

/**
 * Class used to test AdminExtension.
 */
class AdminExtensionTest extends BaseTest
{
    /**
     * Tests if extension is loaded correctly.
     */
    public function testGetExtension()
    {
        $container = self::createClient()->getContainer();
        /** @var AdminExtension $extension */
        $extension = $container->get('ongr_admin.twig.admin_extension');
        $this->assertInstanceOf(
            'ONGR\AdminBundle\Twig\AdminExtension',
            $extension,
            'extension has wrong instance.'
        );
        $this->assertNotEmpty($extension->getFunctions(), 'extension does not have functions defined.');
    }

    /**
     * Gets a UserSettingsManager mock.
     *
     * @param bool $authenticated
     *
     * @return UserSettingsManager
     */
    protected function getSettingsManagerMock($authenticated)
    {
        $settingsManager = $this->getMockBuilder('ONGR\AdminBundle\Settings\UserSettingsManager')
            ->disableOriginalConstructor()
            ->setMethods(['isAuthenticated'])
            ->getMock();

        $settingsManager->expects($this->once())->method('isAuthenticated')->willReturn($authenticated);

        return $settingsManager;
    }

    /**
     * Returns mock of sessionless token.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getTokenMock()
    {
        return $this->getMockBuilder('ONGR\\AdminBundle\\Security\\Authentication\\Token\\SessionlessToken')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Data provider for testShowSetting.
     *
     * @return array[]
     */
    public function showSettingData()
    {
        // Case #0 not authenticated.
        $expectedOutput = '';
        $out[] = [$expectedOutput, 'test', false];

        // Case #1 default type (string).
        $expectedOutput = <<<HEREDOC
<a href="http://localhost/setting/test/edit?type=string" class="btn btn-default pull-right" title="Edit test">
    <span class="glyphicon glyphicon-wrench"></span>
</a>
HEREDOC;
        $out[] = [$expectedOutput, 'test', true];

        // Case #2 custom type (array).
        $expectedOutput = <<<HEREDOC
<a href="http://localhost/setting/test/edit?type=array" class="btn btn-default pull-right" title="Edit test">
    <span class="glyphicon glyphicon-wrench"></span>
</a>
HEREDOC;
        $out[] = [$expectedOutput, 'test', true, 'array'];

        return $out;
    }

    /**
     * Test getPriceList().
     *
     * @param string    $expectedOutput
     * @param string    $settingName
     * @param bool      $isAuthenticated
     * @param string    $type
     *
     * @dataProvider showSettingData
     */
    public function testShowSetting($expectedOutput, $settingName, $isAuthenticated, $type = null)
    {
        $container = self::createClient()->getContainer();
        $securityContext = $container->get('ongr_admin.authentication.sessionless_security_context');
        $securityContext->setToken($this->getTokenMock());
        $settingsManager = $container->get('ongr_admin.settings.user_settings_manager');
        $settingsManager->setSettingsFromForm(['ongr_admin_live_settings' => true]);

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
     * Test for getAdminSetting().
     */
    public function testGetAdminSetting()
    {
        $expectedValue = 'foo-bar';

        $settingContainer = $this->getMock('ONGR\AdminBundle\Settings\Common\SettingsContainerInterface');
        $settingContainer->expects($this->once())->method('get')->with('test')->willReturn($expectedValue);

        $extension = new AdminExtension(null);
        $extension->setSettingsContainer($settingContainer);

        $this->assertEquals($expectedValue, $extension->getAdminSetting('test'));
    }

    /**
     * Test for getAdminSetting() in case setting was not found.
     */
    public function testGetAdminSettingException()
    {
        $settingContainer = $this->getMock('ONGR\AdminBundle\Settings\Common\SettingsContainerInterface');
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
