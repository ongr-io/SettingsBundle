<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Environment variables pass, overrides default variables with environment ones.
 */
class EnvironmentVariablesPass implements CompilerPassInterface
{
    /**
     * Finds environment variables prefixed with ongr__ and changes default ones.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        foreach ($_SERVER as $key => $value) {
            if (0 === strpos($key, 'ongr__')) {
                $param = strtolower(str_replace('__', '.', substr($key, 6)));
                $container->setParameter($param, $value);
            }
        }
    }
}
