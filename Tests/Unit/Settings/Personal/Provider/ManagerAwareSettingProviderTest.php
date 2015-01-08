<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Unit\Settings\Provider;

use ONGR\SettingsBundle\Settings\Personal\Provider\ManagerAwareSettingProvider;

/**
 * Tests for ManagerAwareSettingProvider.
 */
class ManagerAwareSettingProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Checks if exception if thrown if we call getSetting without setting the session model.
     *
     * @expectedException \LogicException
     * @expectedExceptionMessage setManager must be called before getSettings.
     */
    public function testGetSettings()
    {
        $provider = new ManagerAwareSettingProvider();
        $provider->getSettings();
    }

    /**
     * Checks if constructor and domain getter is working as expected.
     */
    public function testGetDomain()
    {
        // Default one should be set.
        $provider = new ManagerAwareSettingProvider();
        $this->assertEquals('default', $provider->getProfile());

        // Custom one should be set.
        $provider = new ManagerAwareSettingProvider('custom');
        $this->assertEquals('custom', $provider->getProfile());
    }
}
