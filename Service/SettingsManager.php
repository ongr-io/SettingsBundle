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
     * @param string       $name
     * @param string|array $value
     * @param string       $profile
     *
     * @throws \LogicException
     */
    public function set($name, $value, $profile = 'default')
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
        $setting->setId($profile . '_' . $name);
        $setting->name = $name;
        $setting->description = 'ongr_admin.' . $this->translator->trans($name);
        $setting->data = (object)['value' => $value];
        $setting->type = $type;
        $setting->profile = $profile;

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
     * Copy a setting to the new profile.
     *
     * @param Setting $setting
     * @param string  $newProfile
     */
    public function duplicate(Setting $setting, $newProfile)
    {
        $newSetting = clone $setting;

        $newSetting->setId($newProfile . '_' . $setting->name);
        $newSetting->profile = $newProfile;

        $this->save($newSetting);
    }

    /**
     * Returns setting model by name and profile or creates new if $mustExist is set to FALSE.
     *
     * @param string $name
     * @param string $profile
     * @param bool   $mustExist
     * @param string $type
     *
     * @throws Exception
     *
     * @return Setting
     */
    public function get($name, $profile = 'default', $mustExist = true, $type = 'string')
    {
        try {
            $setting = $this->repo->find($profile . '_' . $name);
        } catch (Exception $exception) {
            if ($mustExist == true) {
                throw $exception;
            }

            $setting = $this->createSetting($name, $profile, $type);
        }

        return $setting;
    }

    /**
     * Creates new setting object.
     *
     * @param string $name
     * @param string $profile
     * @param string $type
     *
     * @return Setting
     */
    protected function createSetting($name, $profile, $type)
    {
        $setting = new Setting();
        $setting->setId($profile . '_' . $name);
        $setting->name = $name;
        $setting->profile = $profile;
        $setting->type = $type;

        if ($type == 'array') {
            $setting->data['value'] = [];
        }

        return $setting;
    }
}
