<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\DependencyInjection\Security;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;

/**
 * Firewalls factory class. Creates and attaches auth listeners for each firewall.
 */
class SessionlessAuthenticationFactory implements SecurityFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(
        ContainerBuilder $container,
        $id,
        $config,
        $userProvider,
        $defaultEntryPoint
    ) {
        $providerId = 'ongr_settings.firewall.provider.sessionless_authentication.' . $id;
        $container
                ->setDefinition(
                    $providerId,
                    new DefinitionDecorator('ongr_settings.authentication.sessionless_authentication_provider')
                )
                ->replaceArgument(1, new Reference($userProvider));

        $listenerId = 'ongr_settings.firewall.listener.sessionless_authentication.' . $id;
        $container->setDefinition(
            $listenerId,
            new DefinitionDecorator('ongr_settings.authentication.firewall.listener')
        );

        return [$providerId, $listenerId, $defaultEntryPoint];
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return 'pre_auth';
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return 'ongr_sessionless_authentication';
    }

    /**
     * {@inheritdoc}
     */
    public function addConfiguration(NodeDefinition $node)
    {
    }
}
