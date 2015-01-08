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

use ONGR\SettingsBundle\Settings\General\GeneralSettingsManager;

/**
 * Class SettingExtension to show settings value on twig.
 */
class GeneralSettingWidgetExtension extends \Twig_Extension
{
    /**
     * Extension name
     */
    const NAME = 'setting_extension';

    /**
     * @var GeneralSettingsManager
     */
    private $generalSettingsManager;

    /**
     * @param GeneralSettingsManager $GeneralSettingsManager
     */
    public function __construct($GeneralSettingsManager)
    {
        $this->generalSettingsManager = $GeneralSettingsManager;
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
     * @param bool   $mustAuthorize
     *
     * @return mixed
     */
    public function getSettingEnabled($settingName, $mustAuthorize = true)
    {
        return $this->generalSettingsManager->getSettingEnabled($settingName, $mustAuthorize);
    }
}
