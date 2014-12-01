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

use ONGR\AdminBundle\Exception\SettingNotFoundException;
use ONGR\AdminBundle\Settings\Common\SettingsContainerInterface;
use ONGR\AdminBundle\Settings\Admin\AdminSettingsManager;
use Psr\Log\LoggerAwareTrait;

/**
 * Extension for twig, which allows you to show/hide setting edit button depending on authorization.
 */
class SettingWidgetExtension extends \Twig_Extension
{
    use LoggerAwareTrait;

    /**
     * Extension name
     */
    const NAME = 'admin_extension';

    /**
     * @var AdminSettingsManager
     */
    protected $adminSettingsManager;

    /**
     * @var SettingsContainerInterface
     */
    protected $settingContainer;

    /**
     * @var string
     */
    protected $template;

    /**
     * Constructor.
     *
     * @param AdminSettingsManager $adminSettingsManager
     * @param string               $template
     */
    public function __construct($adminSettingsManager, $template = 'ONGRAdminBundle:Controls:edit_setting.html.twig')
    {
        $this->adminSettingsManager = $adminSettingsManager;
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
                    'is_safe' => ['html']
                ]
            ),
            new \Twig_SimpleFunction('ongr_show_setting_value', [$this, 'getAdminSetting']),
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
        if (!$this->adminSettingsManager->isAuthenticated()) {
            return '';
        }

        return $environment->render(
            $this->template,
            [
                'setting' => $settingName,
                'type' => $type,
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
    public function getAdminSetting($name)
    {
        try {
            return $this->settingContainer->get($name);
        } catch (SettingNotFoundException $exception) {
            $this->logger && $this->logger->notice("Template requested non-existing setting '{$name}'.");
        }

        return null;
    }
}
