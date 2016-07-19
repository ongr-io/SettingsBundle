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
use Doctrine\Common\Cache\PhpFileCache;
use ONGR\SettingsBundle\Document\Setting;
use ONGR\SettingsBundle\Service\SettingsManager;

/**
 * Class SettingExtension to show settings value on twig.
 */
class SettingExtension extends \Twig_Extension
{
    /**
     * Extension name
     */
    const NAME = 'ongr_setting_extension';

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
            new \Twig_SimpleFunction('ongr_setting', [$this, 'getSettingValue']),
        ];
    }

    /**
     * @param string $name
     * @param bool   $default
     *
     * @return mixed
     */
    public function getSettingValue($name, $default = false)
    {
        return $this->manager->getValue($name, $default);
    }
}
