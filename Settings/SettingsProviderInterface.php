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

namespace ONGR\AdminBundle\Settings;

/**
 * This interface provides all app settings
 */
interface SettingsProviderInterface
{
    /**
     * Returns all app settings as array in key : value pairs
     *
     * @return array
     */
    public function getSettings();

    /**
     * Returns name of provided settings domain
     *
     * @return string
     */
    public function getDomain();
}
