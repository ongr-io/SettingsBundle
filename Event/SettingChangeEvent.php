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

namespace ONGR\AdminBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Event for setting create/update/delete action
 */
class SettingChangeEvent extends Event
{
    /**
     * @var string
     */
    protected $action;

    /**
     * Constructor
     *
     * @param $action
     */
    public function __construct($action)
    {
        $this->action = $action;
    }
}
