<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Unit\Twig;

use ONGR\SettingsBundle\Twig\GeneralSettingsWidgetExtension;
use ONGR\SettingsBundle\Settings\Personal\PersonalSettingsManager;

class GeneralSettingsWidgetExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests if extension has functions.
     */
    public function testFunctions()
    {
        $extension = $this->getSettingExtension();
        $this->assertNotEmpty($extension->getFunctions(), 'Setting extension should have functions.');
    }

    /**
     * Returns setting extension.
     *
     * @return GeneralSettingsWidgetExtension
     */
    protected function getSettingExtension()
    {
        /** @var PersonalSettingsManager $manager */
        $manager = $this
            ->getMockBuilder('ONGR\SettingsBundle\Settings\Personal\PersonalSettingsManager')
            ->disableOriginalConstructor()
            ->getMock();

        return new GeneralSettingsWidgetExtension($manager);
    }
}
