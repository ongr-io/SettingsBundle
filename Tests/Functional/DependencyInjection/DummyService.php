<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Functional\DependencyInjection;

/**
 * Dummy service for integration test.
 */
class DummyService
{
    /**
     * @var string String to store setting.
     */
    protected $setting;

    /**
     * Set setting.
     *
     * @param string $setting
     */
    public function setSetting1($setting)
    {
        $this->setting = $setting;
    }

    /**
     * Get setting.
     *
     * @return string
     */
    public function getSetting1()
    {
        return $this->setting;
    }
}
