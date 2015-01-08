<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace ONGR\SettingsBundle\Tests\Functional;

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
            'ongr_settings.twig.image_path_extension' => 'ONGR\SettingsBundle\Twig\ImagePathExtension',
            'ongr_settings.twig.encryption_extension' => 'ONGR\SettingsBundle\Twig\EncryptionExtension',
            'ongr_settings.twig.wrapper_extension' => 'ONGR\SettingsBundle\Twig\WrapperExtension',
            'ongr_settings.twig.hidden_extension' => 'ONGR\SettingsBundle\Twig\HiddenExtension',
        ];

        // Settings config file (services/settings.yml).
        $servicesSettings = [
            'ongr_settings.settings.settings_structure' => 'ONGR\SettingsBundle\Settings\General\SettingsStructure',
            'ongr_settings.settings.general_settings_manager' =>
                'ONGR\SettingsBundle\Settings\General\GeneralSettingsManager',
        ];

        // Authentication config file (services/auth.yml).
        $servicesAuth = [
            'ongr_settings.authentication.authentication_cookie_service' =>
                'ONGR\SettingsBundle\Security\Authentication\Cookie\SessionlessAuthenticationCookieService',

            'ongr_settings.authentication.sessionless_authentication_provider' =>
                'ONGR\SettingsBundle\Security\Authentication\Provider\SessionlessAuthenticationProvider',

            'ongr_settings.authentication.signature_generator' =>
                'ONGR\SettingsBundle\Security\Authentication\Cookie\SessionlessSignatureGenerator',

            'ongr_settings.authentication.sessionless_security_context' =>
                'ONGR\SettingsBundle\Security\Core\SessionlessSecurityContext',

            'ongr_settings.authentication.sessionless_cookie_listener' =>
                'ONGR\SettingsBundle\EventListener\Security\SessionlessCookieListener',
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
