<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Functional\Settings;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use ONGR\SettingsBundle\Tests\Fixtures\Security\LoginTestHelper;

class AuthenticationTest extends WebTestCase
{
    /**
     * When accessing not a admin-user settings page, no ES requests must be made.
     *
     * No index has been created in this test, so there will be an uncaught exception, if any queries were executed.
     *
     * @see \ONGR\SettingsBundle\Settings\Personal\PersonalProfilesProvider::getSettings
     */
    public function testRequest()
    {
        $client = static::createClient();
        $loginHelper = new LoginTestHelper($client);
        $client = $loginHelper->loginAction('test', 'test');
        $response = $client->getResponse();

        $this->assertContains('already logged in', $response->getContent());
        $this->assertContains('General Settings', $response->getContent());
    }
}
