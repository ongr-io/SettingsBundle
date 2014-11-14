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

namespace ONGR\AdminBundle\Tests\Functional\Settings\Provider;

use ONGR\AdminBundle\Settings\Provider\SessionModelAwareProvider;

/**
 * Tests for SessionModelAwareProvider
 */
class SessionModelAwareProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Checks if exception if thrown if we call getSetting without setting the session model
     *
     * @expectedException \LogicException
     * @expectedExceptionMessage setSessionModel must be called before getSettings.
     */
    public function testGetSettings()
    {
        $provider = new SessionModelAwareProvider();
        $provider->getSettings();
    }

    /**
     * Checks if constructor and domain getter is working as expected
     */
    public function testGetDomain()
    {
        // default one should be set
        $provider = new SessionModelAwareProvider();
        $this->assertEquals('default', $provider->getDomain());

        // custom one should be set
        $provider = new SessionModelAwareProvider('custom');
        $this->assertEquals('custom', $provider->getDomain());
    }
}
