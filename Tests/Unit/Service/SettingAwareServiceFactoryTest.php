<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Unit\Service;

use ONGR\AdminBundle\FlashBag\DirtyFlashBag;
use ONGR\AdminBundle\Service\SettingAwareServiceFactory;
use ONGR\AdminBundle\Exception\SettingNotFoundException;
use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use stdClass;

class SettingAwareServiceFactoryTest extends ElasticsearchTestCase
{
    /**
     * @var $UnderscoreEscaper object for testing.
     */
    private $testObject;

    /**
     * Set Up for test.
     */
    public function setUp()
    {
        $this->testObject = $this->getMock('stdClass', ['setTestMethod']);
    }

    /**
     * Tests get method.
     */
    public function testGetMethod()
    {
        $settingAwareServiceFactory = new SettingAwareServiceFactory($this->getSettingMock());

        $callMap = [
            'testMethod' => null,
            'setTestMethod' => 'setTestMethod',
        ];

        $this->assertEquals(
            $this->testObject,
            $settingAwareServiceFactory->get($callMap, $this->testObject)
        );
    }

    /**
     * Tests get method logger.
     */
    public function testGetMethodLogger()
    {
        $settingAwareServiceFactory = $this->getSettingAwareServiceFactory();

        $logger = $this->getMock('Psr\Log\LoggerInterface');

        $logger->expects(
            $this->once()
        )->method('notice')
            ->with("Setting 'testMethod' was not found.");

        $settingAwareServiceFactory->setLogger($logger);

        $callMap = [
            'testMethod' => null,
        ];

        $settingAwareServiceFactory->get($callMap, $this->testObject);
    }

    /**
     * Tests get method logic exception.
     *
     * @expectedException LogicException
     */
    public function testGetMethodException()
    {
        $settingAwareServiceFactory = new SettingAwareServiceFactory($this->getSettingMock());
        $callMap = [
            'key' => null,
        ];

        $settingAwareServiceFactory->get($callMap, $this->testObject);
    }

    /**
     *  Returns mock of Setting.
     *
     * @return Setting
     */
    protected function getSettingMock()
    {
        return $this->getMock('ONGR\AdminBundle\Settings\Common\SettingsContainerInterface');
    }

    /**
     *  Returns mock of Setting.
     *
     * @return Setting
     */
    protected function getSettingAwareServiceFactory()
    {
        $settingsContainer = $this->getSettingMock();

        $settingsContainer->expects(
            $this->at(0)
        )->method('get')
            ->will($this->throwException(new SettingNotFoundException));

        return new SettingAwareServiceFactory($settingsContainer);
    }
}
