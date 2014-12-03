<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Unit\Settings\Admin;

use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;

/**
 * Test how AdminProfilesProvider collects Admin settings from ES.
 */
class AdminProfilesProviderTest extends ElasticsearchTestCase
{
    /**
     * Test method getSettings.
     */
    public function testGetSettings()
    {
        $manager = $this->getContainer()->get('ongr_admin.admin_profiles_provider');
        $this->assertEquals($manager->getSettings(), []);
    }
}
