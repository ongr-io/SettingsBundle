<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Service;

//use ONGR\AdminBundle\Event\SettingChangeEvent;
//use ONGR\AdminBundle\Model\SettingModel;
//use ONGR\DDALBundle\Exception\DocumentNotFoundException;
//use ONGR\DDALBundle\Session\SessionModelAwareInterface;
//use ONGR\DDALBundle\Session\SessionModelInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;

class SettingsManager
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Overwrites setting with given name.
     *
     * @param string        $name
     * @param string|array  $value
     * @param string        $domain
     *
     * @throws \LogicException
     */
    public function set($name, $value, $domain = 'default')
    {
        //TODO: implement setting settings
    }

    /**
     * Saves setting.
     *
     * @param SettingModel $model
     */
    public function save(SettingModel $model)
    {
        //TODO: implement saving settings
    }

    /**
     * Removes a setting.
     *
     * @param SettingModel $model
     */
    public function remove(SettingModel $model)
    {
        //TODO: implement removing settings
    }

    /**
     * Copy a setting to the new domain.
     *
     * @param SettingModel $setting
     * @param string       $newDomain
     */
    public function duplicate(SettingModel $setting, $newDomain)
    {
        //TODO: implement duplicating settings
    }

    /**
     * Returns setting model by name and domain or creates new if $mustExist is set to FALSE.
     *
     * @param string $name
     * @param string $domain
     * @param bool   $mustExist
     * @param string $type
     *
     * @return SettingModel
     */
    public function get($name, $domain = 'default', $mustExist = true, $type = 'string')
    {
        //TODO: implement getting settings
        return [];
    }

    /**
     * Creates new setting object.
     *
     * @param string $name
     * @param string $domain
     * @param string $type
     *
     * @return SettingModel
     */
    protected function createSetting($name, $domain, $type)
    {
        //TODO: implement creating setting
    }
}
