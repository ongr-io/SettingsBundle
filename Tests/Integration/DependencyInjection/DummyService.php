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

namespace ONGR\AdminBundle\Tests\Integration\DependencyInjection;

/**
 * Dummy service for integration test
 */
class DummyService
{
    protected $setting;

    public function setSetting1($setting)
    {
        $this->setting = $setting;
    }

    public function getSetting1()
    {
        return $this->setting;
    }
}
