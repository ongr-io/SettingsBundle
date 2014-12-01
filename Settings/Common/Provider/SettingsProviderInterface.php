<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Settings\Common\Provider;

/**
 * This interface provides all app settings.
 */
interface SettingsProviderInterface
{
    /**
     * Returns all app settings as array in key : value pairs.
     *
     * @return array
     */
    public function getSettings();

    /**
     * Returns name of provided settings profile.
     *
     * @return string
     */
    public function getProfile();
}
