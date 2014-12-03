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

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Test how AdminProfilesProvider collects Admin settings from ES.
 */
class AdminProfilesProviderTest extends WebTestCase {

    private $container;

    /**
     * Get Container.
     */
    protected function getServiceContainer()
    {
        if ($this->container === null) {
            $this->container = self::createClient()->getContainer();
        }

        return $this->container;
    }

    /**
     * Get Service init.
     */
    public function testGetAdminProfilesProvider() {
        $manager = $this->getServiceContainer()->get('ongr_admin.admin_profiles_provider');
        $this->assertInstanceOf('ONGR\AdminBundle\Settings\Admin\AdminProfilesProvider', $manager,'');
    }

    /**
     * Test method getSettings.
     */
    public function testGetSettings() {
        $manager = $this->getServiceContainer()->get('ongr_admin.admin_profiles_provider');
        $this->assertEquals( $manager->getSettings() , [] );
    }

}
 