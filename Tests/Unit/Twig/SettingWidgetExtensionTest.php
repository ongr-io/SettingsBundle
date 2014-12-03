<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Unit\Twig;

use ONGR\AdminBundle\Twig\SettingWidgetExtension;
use ONGR\AdminBundle\Settings\Admin\AdminSettingsManager;

class SettingWidgetExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Returns setting extension.
     *
     * @return SettingWidgetExtension
     */
    protected function getSettingExtension()
    {
        /** @var AdminSettingsManager $manager */
        $manager = $this
            ->getMockBuilder('ONGR\AdminBundle\Settings\Admin\AdminSettingsManager')
            ->disableOriginalConstructor()
            ->getMock();

        return new SettingWidgetExtension($manager);
    }

    /**
     * Tests if extension has functions.
     */
    public function testFunctions()
    {
        $extension = $this->getSettingExtension();
        $this->assertNotEmpty($extension->getFunctions(), 'Setting extension should have functions.');
    }
}
