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

namespace Fox\UtilsBundle\Tests\Functional\Settings;

use Fox\UtilsBundle\Settings\UserSettingsManager;

class UserSettingsManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test setSettingsFromCookie method.
     */
    public function testSetSettingsFromCookie()
    {
        $manager = new UserSettingsManager(null, null);
        $manager->setSettingsFromCookie(['foo']);
        $this->assertEquals(['foo'], $manager->getSettings());
    }
}
