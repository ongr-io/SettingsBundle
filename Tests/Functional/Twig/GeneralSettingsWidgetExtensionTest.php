<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Functional\Twig;

use ONGR\SettingsBundle\Exception\SettingNotFoundException;
use ONGR\SettingsBundle\Settings\Personal\PersonalSettingsManager;
use ONGR\SettingsBundle\Twig\GeneralSettingsWidgetExtension;
use ONGR\SettingsBundle\Tests\Fixtures\Security\LoginTestHelper;
use ONGR\SettingsBundle\Settings\General\SettingsContainerInterface;
use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;

/**
 * Class used to test GeneralSettingsExtension.
 */
class GeneralSettingsWidgetExtensionTest extends ElasticsearchTestCase
{
    /**
     * @var Client
     */
    private $client;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->client = new LoginTestHelper(static::createClient());
    }

    /**
     * Tests if extension is loaded correctly.
     */
    public function testGetExtension()
    {
        $container = $this->getContainer();
        /** @var GeneralSettingsWidgetExtension $extension */
        $extension = $container->get('ongr_settings.twig.personal_settings_extension');
        $this->assertInstanceOf(
            'ONGR\SettingsBundle\Twig\PersonalSettingWidgetExtension',
            $extension,
            'extension has wrong instance.'
        );
        $this->assertNotEmpty($extension->getFunctions(), 'extension does not have functions defined.');
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
        $expectedOutput = <<<'NOWDOC'
<a href="http://localhost/admin/setting/test/edit?type=string" class="btn btn-default pull-right" title="Edit test">
    <span class="glyphicon glyphicon-wrench"></span>
</a>
NOWDOC;
        $out[] = [$expectedOutput, 'test', true];

        // Case #2 custom type (array).
        $expectedOutput = <<<'NOWDOC'
<a href="http://localhost/admin/setting/test/edit?type=array" class="btn btn-default pull-right" title="Edit test">
    <span class="glyphicon glyphicon-wrench"></span>
</a>
NOWDOC;
        $out[] = [$expectedOutput, 'test', true, 'array'];

        return $out;
    }

    /**
     * Test getPriceList().
     *
     * @param string $expectedOutput
     * @param string $settingName
     * @param bool   $isAuthenticated
     * @param string $type
     *
     * @dataProvider showSettingData
     */
    public function testShowSetting($expectedOutput, $settingName, $isAuthenticated, $type = null)
    {
        $container = static::createClient()->getContainer();
        $securityContext = $container->get('security.token_storage');
        $securityContext->setToken($this->getTokenMock());

        $settingsManager = $container->get('ongr_settings.settings.personal_settings_manager');
        $settingsManager->setSettingsFromForm(['ongr_settings_live_settings' => true]);

        // Login.
        $client = $this->client->loginAction('test', 'test');

        // Visit settings page.
        $crawler = $client->request('GET', '/settings/settings');

        // Select and submit settings form.
        $buttonNode = $crawler->selectButton('settings_submit');
        $form = $buttonNode->form();
        $form['settings[ongr_settings_live_settings]']->tick();
        $client->submit($form);

        // Call controller with params to generate twig.
        $client->request('GET', '/test/twiggeneral');

        $this->assertContains('count_per_page', $client->getResponse()->getContent());
    }

    /**
     * Test for getPersonalSetting().
     */
    public function testGetGeneralSetting()
    {
        $expectedValue = 'foo-bar';

        $settingContainer = $this->getMock('ONGR\SettingsBundle\Settings\General\SettingsContainerInterface');
        $settingContainer->expects($this->once())->method('get')->with('test')->willReturn($expectedValue);

        $extension = new GeneralSettingsWidgetExtension(null);
        $extension->setSettingsContainer($settingContainer);

        $this->assertEquals($expectedValue, $extension->getPersonalSetting('test'));
    }

    /**
     * Test for getPersonalSetting() in case setting was not found.
     */
    public function testGetGeneralSettingException()
    {
        $settingContainer = $this->getMock('ONGR\SettingsBundle\Settings\General\SettingsContainerInterface');
        $settingContainer
            ->expects($this->once())
            ->method('get')
            ->with('test')
            ->willThrowException(new SettingNotFoundException());

        $extension = new GeneralSettingsWidgetExtension(null);
        $extension->setSettingsContainer($settingContainer);

        $this->assertNull($extension->getPersonalSetting('test'));
    }

    /**
     * Gets a PersonalSettingsManager mock.
     *
     * @param bool $authenticated
     *
     * @return PersonalSettingsManager
     */
    protected function getSettingsManagerMock($authenticated)
    {
        $settingsManager = $this->getMockBuilder('ONGR\SettingsBundle\Settings\Personal\PersonalSettingsManager')
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
        return $this->getMockBuilder('ONGR\\SettingsBundle\\Security\\Authentication\\Token\\SessionlessToken')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
