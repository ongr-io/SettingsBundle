<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Twig;

use ONGR\AdminBundle\Settings\Admin\AdminSettingsManager;

/**
 * Class SettingExtension to show settings value on twig.
 */
class AdminSettingWidgetExtension extends \Twig_Extension
{
    /**
     * Extension name
     */
    const NAME = 'setting_extension';

    /**
     * @var AdminSettingsManager
     */
    private $adminSettingsManager;

    /**
     * @param AdminSettingsManager $adminSettingsManager
     */
    public function __construct($adminSettingsManager)
    {
        $this->adminSettingsManager = $adminSettingsManager;
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
        return $this->adminSettingsManager->getSettingEnabled($settingName, $mustAuthorize);
    }
}
