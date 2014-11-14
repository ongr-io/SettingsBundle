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

namespace ONGR\AdminBundle\Tests\Integration\Settings;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthenticationTest extends WebTestCase
{
    /**
     * When accessing not a power-user settings page, no DDAL requests must be made.
     *
     * No index has been created in this test, so there will be an uncaught exception, if any queries were executed.
     *
     * @see \ONGR\AdminBundle\Settings\PowerUserDomainsProvider::getSettings
     */
    public function testRequest()
    {
        $client = self::createClient();
        $client->request('GET', '/power-user/login');
    }
}
