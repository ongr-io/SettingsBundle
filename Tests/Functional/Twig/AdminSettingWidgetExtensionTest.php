<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Functional\Twig;

use ONGR\AdminBundle\Tests\Functional\CookieTestHelper;
use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Tests for testing settings TWIG extension functionality.
 */
class AdminSettingWidgetExtensionTest extends ElasticsearchTestCase
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var Client
     */
    private $client;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->client = self::createClient();
        $this->twig = $this->client->getContainer()->get('twig');
    }

    /**
     * Cases for testExtensionInTemplate.
     *
     * @return array
     */
    public function getExtensionInTemplateCases()
    {
        $cases = [];

        // Case 0: Normal setting, extension should return value.
        $cases[] = [['ongr_admin_user_settings' => ['foo_setting' => true]], 'foo_setting', true, true];
        // Case 1: Normal setting (value false). Should return value.
        $cases[] = [['ongr_admin_user_settings' => ['foo_setting' => false]], 'foo_setting', true, false];
        // Case 2: Setting missing, should return false.
        $cases[] = [['ongr_admin_user_settings' => ['foo_setting' => true]], 'foo_setting_2', true, false];
        // Case 3: Not authorized, normal setting, should return false.
        $cases[] = [['ongr_admin_user_settings' => ['foo_setting' => true]], 'foo_setting', false, false];
        // Case 4: Not authorized, normal setting (value false), should return false.
        $cases[] = [['ongr_admin_user_settings' => ['foo_setting' => false]], 'foo_setting', false, false];
        // Case 5: Not authorized, setting missing, should return false.
        $cases[] = [['ongr_admin_user_settings' => ['foo_setting' => false]], 'foo_setting_2', false, false];
        // Case 6: Not authorized, normal setting, should return value when command does not require authorization.
        $cases[] = [['ongr_ab_settings' => ['foo_setting_4' => true]], 'foo_setting_4', false, true, false];

        return $cases;
    }

    /**
     * Test TWIG command usage.
     *
     * @param array  $cookieSettings
     * @param string $testSettingName
     * @param bool   $shouldAuthorize
     * @param bool   $expectedResult
     * @param bool   $authorizedCommand
     *
     * @dataProvider getExtensionInTemplateCases()
     */
    public function testExtensionInTemplate(
        $cookieSettings,
        $testSettingName,
        $shouldAuthorize,
        $expectedResult,
        $authorizedCommand = true
    ) {
        if ($shouldAuthorize) {
            CookieTestHelper::setAuthenticationCookie($this->client);
        }

        CookieTestHelper::setSettingsCookie($this->client, $cookieSettings);

        // Run all listeners. A hack.
        try {
            $this->client->request('GET', '/');
        } catch (NotFoundHttpException $e) {
            // Not needed.
        }

        $template = "{% if ongr_setting_enabled('$testSettingName'" .
            ($authorizedCommand ? '' : ', false') .
            ') %}foo_true{% else %}foo_false{% endif %}';

        $loader = $this->twig->getLoader();
        $this->twig->setLoader(new \Twig_Loader_String());
        $renderedResult = $this->twig->render($template);
        $this->twig->setLoader($loader);

        $this->assertEquals($expectedResult ? 'foo_true' : 'foo_false', $renderedResult);
    }
}
