<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Functional\Settings;

use ONGR\AdminBundle\Settings\Admin\AdminSettingsManager;

class AdminSettingsManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test setSettingsFromCookie method.
     */
    public function testSetSettingsFromCookie()
    {
        $manager = new AdminSettingsManager(null, null);
        $manager->setSettingsFromCookie(['foo']);
        $this->assertEquals(['foo'], $manager->getSettings());
    }
}
