<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Functional\DependencyInjection\Compiler;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Integration tests for  EnvironmentVariablesPass.
 */
class EnvironmentVariablesPassTest extends WebTestCase
{
    /**
     * Check if parameters are overrode at the right time. I.E. before container freezes.
     */
    public function testOverriding()
    {
        // First load up the default variables and check if they're set.
        $kernel = static::createClient()->getKernel();
        $container = $kernel->getContainer();

        $this->assertEquals(
            'unchanged_param',
            $container->getParameter('ongr_settings.environment_variables_pass_test_param')
        );

        // Now set an env variable and check if it has changed the default one.
        $_SERVER['ongr__ongr_settings__environment_variables_pass_test_param'] = 'successful_change';
        $kernel = static::createClient(['environment' => 'test_alternative'])->getKernel();
        $container = $kernel->getContainer();

        $this->assertEquals(
            'successful_change',
            $container->getParameter('ongr_settings.environment_variables_pass_test_param')
        );
    }
}
