<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Unit\Settings;

use ONGR\SettingsBundle\Settings\Personal\PersonalSettingsManager;
use Stash\Pool;

class PersonalSettingsManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test setSettingsFromCookie method.
     */
    public function testSetSettingsFromCookie()
    {
        $manager = new PersonalSettingsManager(null, null, new Pool());
        $manager->setSettingsFromStash(['foo']);
        $this->assertEquals(['foo'], $manager->getSettings());
    }
}
