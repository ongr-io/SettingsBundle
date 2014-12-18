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

use ONGR\AdminBundle\Service\SettingAwareServiceFactory;
use ONGR\AdminBundle\Exception\SettingNotFoundException;
use ONGR\AdminBundle\Service\UnderscoreEscaper;
use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;

class SettingAwareServiceFactoryTest extends ElasticsearchTestCase
{
    /**
     * Tests get method.
     */
    public function testGetMethod()
    {
        $settingsContainer = $this->getMock('ONGR\AdminBundle\Settings\Common\SettingsContainerInterface');

        $settingsContainer->expects(
            $this->at(1)
        )->method('get')
            ->will($this->throwException(new SettingNotFoundException));

        $settingAwareServiceFactory = new SettingAwareServiceFactory($settingsContainer);

        $logger = $this->getMock('Psr\Log\LoggerInterface');

        $callMap = [
            'escape' => 'escape',
        ];

        $testObject = new UnderscoreEscaper();

        $this->assertEquals(
            new UnderscoreEscaper(),
            $settingAwareServiceFactory->get($callMap, $testObject)
        );

        $logger->expects(
            $this->once()
        )->method('notice')
            ->with("Setting 'key1' was not found.");

        $settingAwareServiceFactory->setLogger($logger);

        $this->setExpectedException('LogicException');

        /**
         * key1 index write to logger.
         * key2 throw logic exception.
        */

        $callMap = [
            'key1' => 'SettingNotFoundException',
            'key2' => null,
        ];
        $settingAwareServiceFactory->get($callMap, $testObject);
    }
}
