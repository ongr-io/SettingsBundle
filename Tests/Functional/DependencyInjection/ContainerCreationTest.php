<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Functional\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * This class checks if we are able to create container.
 */
class ContainerCreationTest extends WebTestCase
{
    /**
     * Check if we are able to create container without errors.
     */
    public function testCreation()
    {
        self::createClient()->getContainer();
    }

    /**
     * Checks if admin using category was injected.
     */
    public function testPersonalUserCategory()
    {
        $container = self::createClient()->getContainer();
        $this->assertArrayHasKey(
            'ongr_settings_settings',
            $container->getParameter('ongr_settings.settings.categories')
        );
        $this->assertArrayHasKey(
            'ongr_settings_profiles',
            $container->getParameter('ongr_settings.settings.categories')
        );
    }

    /**
     * Checks if admin using settings were injected.
     */
    public function testPersonalUserSettings()
    {
        $kernel = self::createClient(['environment' => 'test_container_creation'])->getKernel();
        $container = $kernel->getContainer();

        $this->assertArrayHasKey(
            'ongr_settings_live_settings',
            $container->getParameter('ongr_settings.settings.settings')
        );
    }
}
