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

namespace Fox\AdminBundle\Settings;

/**
 * This interface provides structure for settings container
 */
interface SettingsContainerInterface
{
    /**
     * @param SettingsProviderInterface $provider
     */
    public function addProvider(SettingsProviderInterface $provider);

    /**
     * Returns current value of setting
     *
     * @param string $setting
     * @return string|array
     *
     * @throws \InvalidArgumentException
     */
    public function get($setting);
}
