<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Twig;

use ONGR\SettingsBundle\Settings\Personal\PersonalSettingsManager;

/**
 * Class SettingExtension to show settings value on twig.
 */
class PersonalSettingWidgetExtension extends \Twig_Extension
{
    /**
     * Extension name
     */
    const NAME = 'personal_settings_extension';

    /**
     * @var PersonalSettingsManager
     */
    private $porsonallSettingsManager;

    /**
     * @param PersonalSettingsManager $personalSettingsManager
     */
    public function __construct($personalSettingsManager)
    {
        $this->porsonallSettingsManager = $personalSettingsManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('ongr_setting_enabled', [$this, 'getSettingEnabled']),
        ];
    }

    /**
     * Return setting value for the current user.
     *
     * @param string $settingName
     *
     * @return mixed
     */
    public function getSettingEnabled($settingName)
    {
        return $this->porsonallSettingsManager->getSettingEnabled($settingName);
    }
}
