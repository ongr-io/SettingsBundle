<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Functional\Settings\Personal;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Test how PersonalProfilesProvider collects General settings from ES.
 */
class PersonalProfilesProviderTest extends WebTestCase
{
    /**
     * @var Container.
     */
    private $container;

    /**
     * Get Service init.
     */
    public function testGetPersonalProfilesProvider()
    {
        $manager = $this->getServiceContainer()->get('ongr_settings.personal_profiles_provider');
        $this->assertInstanceOf('ONGR\SettingsBundle\Settings\Personal\PersonalProfilesProvider', $manager, '');
    }

    /**
     * Get Container.
     *
     * @return Service Service container.
     */
    protected function getServiceContainer()
    {
        if ($this->container === null) {
            $this->container = static::createClient()->getContainer();
        }

        return $this->container;
    }
}
