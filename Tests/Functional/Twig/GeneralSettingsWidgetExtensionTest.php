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

use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use ONGR\SettingsBundle\Exception\SettingNotFoundException;
use ONGR\SettingsBundle\Twig\GeneralSettingsWidgetExtension;
use ONGR\SettingsBundle\Tests\Fixtures\Security\LoginTestHelper;

/**
 * Class used to test GeneralSettingsExtension.
 */
class GeneralSettingsWidgetExtensionTest extends AbstractElasticsearchTestCase
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
        $expectedOutput = '<div></div>';
        $out[] = [$expectedOutput, 'test_count_per_page', false];

        // Case #1 default type (string).
        $expectedOutput = '<a href="http://localhost/settings/setting/count_per_page/edit/test" ';
        $out[] = [$expectedOutput, 'test_count_per_page', true];

        return $out;
    }

    /**
     * Test getPriceList().
     *
     * @param string $expectedOutput
     * @param string $settingId
     * @param bool   $isAuthenticated
     *
     * @dataProvider showSettingData
     */
    public function testShowSetting($expectedOutput, $settingId, $isAuthenticated)
    {
        if ($isAuthenticated) {
            $client = static::createClient(
                [],
                [
                    'PHP_AUTH_USER' => 'admin',
                    'PHP_AUTH_PW'   => 'admin',
                ]
            );
        } else {
            $client = static::createClient();
        }

        $settingTicUrl = 'settings/setting/change/'.base64_encode('ongr_settings_live_settings');
        // Tic the setting
        $client->request('GET', $settingTicUrl);
        $this->assertTrue($client->getResponse()->isOk());

        // Call controller with params to generate twig.
        $client->request('GET', '/test/twiggeneral');

        $this->assertContains($expectedOutput, $client->getResponse()->getContent());
    }

    /**
     * Test for getPersonalSetting() from general settings container.
     */
    public function testGetPersonalSetting()
    {
        $expectedValue = 'foo-bar';

        $settingContainer = $this->getMock('ONGR\SettingsBundle\Settings\General\SettingsContainerInterface');
        $settingContainer->expects($this->once())->method('get')->with('test')->willReturn($expectedValue);

        $extension = new GeneralSettingsWidgetExtension(null);
        $extension->setSettingsContainer($settingContainer);

        $this->assertEquals($expectedValue, $extension->getPersonalSetting('test'));
    }

    /**
     * Test for getPersonalSetting()  from general settings container in case setting was not found.
     */
    public function testGetPersonalSettingException()
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
}
