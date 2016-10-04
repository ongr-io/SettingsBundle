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

class SettingActionEvent extends Event
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $data;

    /**
     * @var
     */
    private $setting;

    public function __construct($name, array $data = null, $setting = null)
    {
        $this->name = $name;
        $this->data = $data;
        $this->setting = $setting;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getSetting()
    {
        return $this->setting;
    }

    /**
     * @param mixed $setting
     */
    public function setSetting($setting)
    {
        $this->setting = $setting;
    }
}