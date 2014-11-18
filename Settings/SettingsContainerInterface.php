<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Settings;

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
     * Returns current value of setting.
     *
     * @param string $setting
     *
     * @return string|array
     *
     * @throws \InvalidArgumentException
     */
    public function get($setting);
}
