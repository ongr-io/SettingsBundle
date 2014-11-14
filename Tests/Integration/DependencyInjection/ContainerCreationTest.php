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

namespace ONGR\AdminBundle\Tests\Integration\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * This class checks if we are able to create container
 */
class ContainerCreationTest extends WebTestCase
{
    /**
     * Check if we are able to create container without errors
     */
    public function testCreation()
    {
        self::createClient()->getContainer();
    }

    /**
     * Checks if power using category was injected
     */
    public function testPowerUserCategory()
    {
        $container = self::createClient()->getContainer();
        $this->assertArrayHasKey('ongr_admin_settings', $container->getParameter('ongr_utils.settings.categories'));
        $this->assertArrayHasKey('ongr_admin_domains', $container->getParameter('ongr_utils.settings.categories'));
    }

    /**
     * Checks if power using settings were injected
     */
    public function testPowerUserSettings()
    {
        $container = self::createClient()->getContainer();
        $this->assertArrayHasKey('ongr_admin_live_settings', $container->getParameter('ongr_utils.settings.settings'));
    }
}
