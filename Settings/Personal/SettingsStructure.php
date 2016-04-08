<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Settings\Personal;

/**
 * Manages settings structure that is displayed for the user.
 *
 * Class is not aware of user selected values, only of structure.
 */
class SettingsStructure
{
    /**
     * @var array
     */
    private $settingsParameter;

    /**
     * @var array
     */
    private $categoriesParameter;

    /**
     * @var array
     */
    private $structure = [];

    /**
     * @param array $settingsParameter
     * @param array $categoriesParameter
     */
    public function __construct(array $settingsParameter, array $categoriesParameter)
    {
        $this->settingsParameter = $settingsParameter;
        $this->categoriesParameter = $categoriesParameter;

        foreach ($settingsParameter as $settingId => $setting) {
            $this->addSetting($settingId, $setting);
        }
    }

    /**
     * Call service's method and store returned additional structure.
     *
     * @param object $service
     * @param string $method
     *
     * @throws \InvalidArgumentException
     */
    public function extractSettings($service, $method)
    {
        $newSettings = $service->$method();
        foreach ($newSettings as $settingId => $setting) {
            $this->addSetting($settingId, $setting);
        }
    }

    /**
     * Add setting to structure recursively.
     *
     * E.g.
     *
     * self::addSetting('setting_id', [
     *     'name' => 'Setting label',
     *     'category' => 'category_id',
     *     'description' => 'Description',
     *     'type' => ['choice', ['expanded' => true, 'multiple' => true], # or 'type' => [$customType, [$options]]
     * ]);
     *
     * @param string $id
     * @param array  $setting
     */
    public function addSetting($id, array $setting)
    {
        $this->structure[$id] = $this->ensureSettingCookie($setting);
    }

    /**
     * Ensure 'cookie' key is set. If not, default value is set.
     *
     * @param array $setting
     *
     * @return array
     */
    public function ensureSettingCookie(array $setting)
    {
        if (!isset($setting['cookie'])) {
            $setting['cookie'] = 'ongr_settings.settings.settings_cookie';
        }

        return $setting;
    }

    /**
     * @return array
     */
    public function getStructure()
    {
        return $this->structure;
    }

    /**
     * @param array $structure
     */
    public function setStructure($structure)
    {
        $this->structure = [];

        foreach ($structure as $id => $setting) {
            $this->addSetting($id, $setting);
        }
    }

    /**
     * @return array
     */
    public function getCategoriesStructure()
    {
        return $this->categoriesParameter;
    }

    /**
     * @param array $categoriesParameter
     */
    public function setCategoriesStructure($categoriesParameter)
    {
        $this->categoriesParameter = $categoriesParameter;
    }
}
