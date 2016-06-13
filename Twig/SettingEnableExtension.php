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
use ONGR\SettingsBundle\Settings\General\SettingsManager;

/**
 * Class SettingExtension to show settings value on twig.
 */
class PersonalSettingWidgetExtension extends \Twig_Extension
{
    /**
     * Extension name
     */
    const NAME = 'setting_enable_extension';

    /**
     * @var SettingsManager
     */
    private $manager;

    /**
     * @param SettingsManager $manager
     */
    public function __construct($manager)
    {
        $this->manager = $manager;
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
            new \Twig_SimpleFunction('ongr_setting_enabled', [$this, 'isSettingEnabled']),
        ];
    }

    /**
     * Return setting value for the current user.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getSettingEnabled($name)
    {
//        return $this->manager->getSettingEnabled($name);
    }
}
