<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle;

use ONGR\AdminBundle\DependencyInjection\Compiler\EnvironmentVariablesPass;
use ONGR\AdminBundle\DependencyInjection\Compiler\ProviderPass;
use ONGR\AdminBundle\DependencyInjection\Compiler\SettingsModifierPass;
use ONGR\AdminBundle\DependencyInjection\Compiler\SettingAwareFactoryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use ONGR\AdminBundle\DependencyInjection\Security\SessionlessAuthenticationFactory;

/**
 * This class is used to register component into Symfony app kernel.
 */
class ONGRAdminBundle extends Bundle
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
		$extension->addSecurityListenerFactory(new SessionlessAuthenticationFactory());
    }
}
