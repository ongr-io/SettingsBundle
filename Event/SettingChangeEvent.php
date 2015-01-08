<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Event for setting create/update/delete action.
 */
class SettingChangeEvent extends Event
{
    /**
     * @var string
     */
    protected $action;

    /**
     * Constructor.
     *
     * @param Action $action
     */
    public function __construct($action)
    {
        $this->action = $action;
    }
}
