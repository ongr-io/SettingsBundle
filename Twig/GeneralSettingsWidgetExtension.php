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

use ONGR\SettingsBundle\Exception\SettingNotFoundException;
use ONGR\SettingsBundle\Settings\General\SettingsContainerInterface;
use ONGR\SettingsBundle\Settings\Personal\PersonalSettingsManager;
use Psr\Log\LoggerAwareTrait;

/**
 * Extension for twig, which allows you to show/hide setting edit button depending on authorization.
 */
class GeneralSettingsWidgetExtension extends \Twig_Extension
{
    use LoggerAwareTrait;

    /**
     * Extension name
     */
    const NAME = 'setting_extension';

    /**
     * @var PersonalSettingsManager
     */
    protected $personalSettingsManager;

    /**
     * @var SettingsContainerInterface.
     */
    protected $settingContainer;

    /**
     * @var string
     */
    protected $template;

    /**
     * Constructor.
     *
     * @param PersonalSettingsManager $personalSettingsManager
     * @param string                  $template
     */
    public function __construct(
        $personalSettingsManager,
        $template = 'ONGRSettingsBundle:Controls:edit_setting.html.twig'
    ) {
        $this->personalSettingsManager = $personalSettingsManager;
        $this->template = $template;
    }

    /**
     * Sets setting container.
     *
     * @param SettingsContainerInterface $settingsContainer
     */
    public function setSettingsContainer(SettingsContainerInterface $settingsContainer)
    {
        $this->settingContainer = $settingsContainer;
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
            new \Twig_SimpleFunction(
                'ongr_show_setting_widget',
                [$this, 'showSetting'],
                [
                    'needs_environment' => true,
                    'is_safe' => ['html'],
                ]
            ),
            new \Twig_SimpleFunction('ongr_show_setting_value', [$this, 'getPersonalSetting']),
        ];
    }

    /**
     * Returns button which links to edit page if it is a user.
     *
     * @param \Twig_Environment $environment
     * @param string            $settingName
     * @param string            $type
     *
     * @return string
     */
    public function showSetting($environment, $settingName, $type = 'string')
    {
        if (!$this->personalSettingsManager->isAuthenticated()) {
            return '';
        }
        $pos = strpos($settingName, '_');
        $profile = substr($settingName, 0, $pos);
        $name = substr($settingName, $pos+1);

        return $environment->render(
            $this->template,
            [
                'setting' => $name,
                'profile' => $profile,
            ]
        );
    }

    /**
     * Returns setting value.
     *
     * @param string $name
     *
     * @return array|string
     */
    public function getPersonalSetting($name)
    {
        try {
            return $this->settingContainer->get($name);
        } catch (SettingNotFoundException $exception) {
            $this->logger && $this->logger->notice("Template requested non-existing setting '{$name}'.");
        }

        return null;
    }
}
