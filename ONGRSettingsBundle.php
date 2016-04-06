<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle;

use ONGR\SettingsBundle\DependencyInjection\Compiler\EnvironmentVariablesPass;
use ONGR\SettingsBundle\DependencyInjection\Compiler\ProviderPass;
use ONGR\SettingsBundle\DependencyInjection\Compiler\SettingsModifierPass;
use ONGR\SettingsBundle\DependencyInjection\Compiler\SettingAwareFactoryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use ONGR\SettingsBundle\DependencyInjection\Security\SessionlessAuthenticationFactory;

/**
 * This class is used to register component into Symfony app kernel.
 */
class ONGRSettingsBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new EnvironmentVariablesPass());
        $container->addCompilerPass(new ProviderPass());
        $container->addCompilerPass(new SettingsModifierPass());
        $container->addCompilerPass(new SettingAwareFactoryPass());

        $extension = $container->getExtension('security');
    }
}
