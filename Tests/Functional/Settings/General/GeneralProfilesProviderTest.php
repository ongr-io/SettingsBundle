<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Functional\Settings\General;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Test how GeneralProfilesProvider collects General settings from ES.
 */
class GeneralProfilesProviderTest extends WebTestCase
{
    /**
     * @var Container.
     */
    private $container;

    /**
     * Get Service init.
     */
    public function testGetAdminProfilesProvider()
    {
        $manager = $this->getServiceContainer()->get('ongr_settings.admin_profiles_provider');
        $this->assertInstanceOf('ONGR\SettingsBundle\Settings\General\GeneralProfilesProvider', $manager, '');
    }

    /**
     * Get Container.
     *
     * @return Service container
     */
    protected function getServiceContainer()
    {
        if ($this->container === null) {
            $this->container = self::createClient()->getContainer();
        }

        return $this->container;
    }
}
