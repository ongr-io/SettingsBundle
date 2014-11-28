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

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;
use ONGR\ElasticsearchBundle\ORM\Repository;
use ONGR\ElasticsearchBundle\ORM\Manager;
use ONGR\AdminBundle\Document\Setting;
use ONGR\AdminBundle\Event\SettingChangeEvent;
use Exception;

/**
 * Class SettingsManager responsible for managing settings actions.
 *
 * @package ONGR\AdminBundle\Service
 */
class SettingsManager
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var Repository
     */
    protected $repo;

    /**
     * Constructor.
     *
     * @param TranslatorInterface      $translator
     * @param EventDispatcherInterface $eventDispatcher
     * @param Manager                  $manager
     */
    public function __construct(
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        Manager $manager
    ) {
        $this->translator = $translator;
        $this->eventDispatcher = $eventDispatcher;
        $this->manager = $manager;
        $this->repo = $this->manager->getRepository('ONGRAdminBundle:Setting');
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
        switch (gettype($value)) {
            case 'boolean':
                $type = Setting::TYPE_BOOLEAN;
                break;
            case 'array':
                $type = Setting::TYPE_ARRAY;
                break;
            case 'object':
                $type = Setting::TYPE_OBJECT;
                break;
            default:
                $type = Setting::TYPE_STRING;
                break;
        }

        $setting = new Setting();
        $setting->setId($domain . '_' . $name);
        $setting->name = $name;
        $setting->description = 'ongr_admin.' . $this->translator->trans($name);
        $setting->data = (object)['value' => $value];
        $setting->type = $type;
        $setting->domain = $domain;

        $this->manager->persist($setting);
        $this->manager->commit();
        $this->manager->flush();

        $this->eventDispatcher->dispatch('ongr_admin.setting_change', new SettingChangeEvent('save'));
    }

    /**
     * Saves setting.
     *
     * @param Setting $setting
     */
    public function save(Setting $setting)
    {
        $this->manager->persist($setting);
        $this->manager->commit();
        $this->manager->flush();
        $this->manager->refresh();

        $this->eventDispatcher->dispatch('ongr_admin.setting_change', new SettingChangeEvent('save'));
    }

    /**
     * Removes a setting.
     *
     * @param Setting $setting
     */
    public function remove(Setting $setting)
    {
        $this->repo->remove($setting->getId());
        $this->manager->flush();
        $this->manager->refresh();

        $this->eventDispatcher->dispatch('ongr_admin.setting_change', new SettingChangeEvent('delete'));
    }

    /**
     * Copy a setting to the new domain.
     *
     * @param Setting $setting
     * @param string  $newDomain
     */
    public function duplicate(Setting $setting, $newDomain)
    {
        $newSetting = clone $setting;

        $newSetting->setId($newDomain . '_' . $setting->name);
        $newSetting->domain = $newDomain;

        $this->save($newSetting);
    }

    /**
     * Returns setting model by name and domain or creates new if $mustExist is set to FALSE.
     *
     * @param string $name
     * @param string $domain
     * @param bool   $mustExist
     * @param string $type
     *
     * @throws Exception
     *
     * @return Setting
     */
    public function get($name, $domain = 'default', $mustExist = true, $type = 'string')
    {
        try {
            $setting = $this->repo->find($domain . '_' . $name);
        } catch (Exception $exception) {
            if ($mustExist == true) {
                throw $exception;
            }

            $setting = $this->createSetting($name, $domain, $type);
        }

        return $setting;
    }

    /**
     * Creates new setting object.
     *
     * @param string $name
     * @param string $domain
     * @param string $type
     *
     * @return Setting
     */
    protected function createSetting($name, $domain, $type)
    {
        $setting = new Setting();
        $setting->setId($domain . '_' . $name);
        $setting->name = $name;
        $setting->domain = $domain;
        $setting->type = $type;

        if ($type == 'array') {
            $setting->data['value'] = [];
        }

        return $setting;
    }
}
