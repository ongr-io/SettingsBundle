<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace ONGR\AdminBundle\Tests\Functional;

use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use Symfony\Component\HttpFoundation\Request;

class ServiceCreationTest extends ElasticsearchTestCase
{
    /**
     * Test if needed services are created correctly.
     */
    public function testServiceCreation()
    {
        $container = self::createClient()->getKernel()->getContainer();
        $container->enterScope('request');
        $container->set('request', new Request(), 'request');

        // Twig config file (services/twig.yml).
        $servicesTwig = [
            'ongr_admin.twig.image_path_extension' => 'ONGR\AdminBundle\Twig\ImagePathExtension',
            'ongr_admin.twig.encryption_extension' => 'ONGR\AdminBundle\Twig\EncryptionExtension',
            'ongr_admin.twig.wrapper_extension' => 'ONGR\AdminBundle\Twig\WrapperExtension',
            'ongr_admin.twig.hidden_extension' => 'ONGR\AdminBundle\Twig\HiddenExtension',
        ];

        // Settings config file (services/settings.yml).
        $servicesSettings = [
            'ongr_admin.settings.settings_structure' => 'ONGR\AdminBundle\Settings\Admin\SettingsStructure',
            'ongr_admin.settings.admin_settings_manager' => 'ONGR\AdminBundle\Settings\Admin\AdminSettingsManager',
        ];

        // Authentication config file (services/sessionless_authentication.yml).
        $servicesAuth = [
            'ongr_admin.authentication.authentication_cookie_service' =>
                'ONGR\AdminBundle\Security\Authentication\Cookie\SessionlessAuthenticationCookieService',

            'ongr_admin.authentication.sessionless_authentication_provider' =>
                'ONGR\AdminBundle\Security\Authentication\Provider\SessionlessAuthenticationProvider',

            'ongr_admin.authentication.signature_generator' =>
                'ONGR\AdminBundle\Security\Authentication\Cookie\SessionlessSignatureGenerator',

            'ongr_admin.authentication.sessionless_authentication_provider' =>
                'ONGR\AdminBundle\Security\Authentication\Provider\SessionlessAuthenticationProvider',

            'ongr_admin.authentication.firewall.listener' =>
                'ONGR\AdminBundle\Security\Authentication\Firewall\SessionlessAuthenticationListener',
        ];

        $services = array_merge($servicesTwig, $servicesSettings, $servicesAuth);

        foreach ($services as $id => $serviceClass) {
            $this->assertTrue($container->has($id), "Does not have '{$id}' service'");
            $this->assertInstanceOf(
                $serviceClass,
                $container->get($id),
                "'{$id}' service is not an instance of '{$serviceClass}'"
            );
        }
    }
}
