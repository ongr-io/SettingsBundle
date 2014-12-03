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

namespace Fox\UtilsBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class ServiceCreationTest extends WebTestCase
{
    /**
     * Test if needed services are created correctly
     */
    public function testServiceCreation()
    {
        $container = self::createClient()->getKernel()->getContainer();
        $container->enterScope('request');
        $container->set('request', new Request(), 'request');

        // Main config file (config/services.yml
        $servicesMain = [
            'fox_crawler.crawler_service'       => 'Fox\UtilsBundle\Service\CrawlerService',
            'fox_utils.templating.helper.pager' => 'Fox\UtilsBundle\Pager\Templating\PagerHelper',
            'fox_utils.file_locker_service'     => 'Fox\UtilsBundle\Service\FileLockService',
            'fox_crawler.scanner_service'       => 'Fox\UtilsBundle\Service\ScanPageService'
        ];

        // Twig config file (services/twig.yml)
        $servicesTwig = [
            'fox_utils.twig.image_path_extension' => 'Fox\UtilsBundle\Twig\ImagePathExtension',
            'fox_utils.twig.checkout_extension'   => 'Fox\UtilsBundle\Twig\CheckoutExtension',
            'fox_utils.twig.price_extension'      => 'Fox\UtilsBundle\Twig\PriceExtension',
            'fox_utils.twig.encryption_extension' => 'Fox\UtilsBundle\Twig\EncryptionExtension',
            'fox_utils.twig.wrapper_extension'    => 'Fox\UtilsBundle\Twig\WrapperExtension',
            'fox_utils.twig.slugify_extension'    => 'Fox\UtilsBundle\Twig\SlugifyExtension',
            'fox_utils.twig.hidden_extension'     => 'Fox\UtilsBundle\Twig\HiddenExtension',
            'fox_utils.twig.setting_extension'    => 'Fox\UtilsBundle\Twig\SettingExtension',
            'fox_utils.twig.extension.pager'      => 'Fox\UtilsBundle\Twig\PagerExtension'
        ];

        // Settings config file (services/settings.yml)
        $servicesSettings = [
            'fox_utils.settings.settings_cookie_service' => 'Fox\UtilsBundle\Settings\SettingsCookieService',
            'fox_utils.settings.settings_structure'      => 'Fox\UtilsBundle\Settings\SettingsStructure',
            'fox_utils.settings.user_settings_manager'   => 'Fox\UtilsBundle\Settings\UserSettingsManager'
        ];

        // Authentication config file (services/auth.yml)
        $servicesAuth = [
            'fox_utils.authentication.authentication_cookie_service' =>
                'Fox\UtilsBundle\Security\Authentication\Cookie\SessionlessAuthenticationCookieService',

            'fox_utils.authentication.sessionless_authentication_provider' =>
                'Fox\UtilsBundle\Security\Authentication\Provider\SessionlessAuthenticationProvider',

            'fox_utils.authentication.signature_generator' =>
                'Fox\UtilsBundle\Security\Authentication\Cookie\SessionlessSignatureGenerator',

            'fox_utils.authentication.sessionless_security_context' =>
                'Fox\UtilsBundle\Security\Core\SessionlessSecurityContext',

            'fox_utils.authentication.sessionless_cookie_listener' =>
                'Fox\UtilsBundle\EventListener\SessionlessCookieListener'
        ];

        $services = array_merge($servicesMain, $servicesTwig, $servicesSettings, $servicesAuth);

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
