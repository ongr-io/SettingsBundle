<?php

/*
 *************************************************************************
 * NFQ eXtremes CONFIDENTIAL
 * [2013] - [2014] NFQ eXtremes UAB
 * All Rights Reserved.
 *************************************************************************
 * NOTICE:
 * All information contained herein is, and remains the property of NFQ eXtremes UAB.
 * Dissemination of this information or reproduction of this material is strictly forbidden
 * unless prior written permission is obtained from NFQ eXtremes UAB.
 *************************************************************************
 */

namespace ONGR\AdminBundle\Twig;

use ONGR\AdminBundle\Exception\SettingNotFoundException;
use ONGR\AdminBundle\Settings\SettingsContainerInterface;
use ONGR\UtilsBundle\Settings\UserSettingsManager;
use Psr\Log\LoggerAwareTrait;

/**
 * Extension for twig, which allows you to show/hide setting edit button depending on authorization
 */
class AdminExtension extends \Twig_Extension
{
    use LoggerAwareTrait;

    /**
     * Extension name
     */
    const NAME = 'admin_extension';

    /**
     * @var UserSettingsManager
     */
    protected $userSettingsManager;

    /**
     * @var SettingsContainerInterface
     */
    protected $settingContainer;

    /**
     * @var string
     */
    protected $template;

    /**
     * Constructor
     *
     * @param UserSettingsManager $userSettingsManager
     * @param string $template
     */
    public function __construct($userSettingsManager, $template = 'FoxAdminBundle:Controls:edit_setting.html.twig')
    {
        $this->userSettingsManager = $userSettingsManager;
        $this->template = $template;
    }

    /**
     * Sets setting container
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
                'ongr_show_setting',
                [$this, 'showSetting'],
                [
                    'needs_environment' => true,
                    'is_safe' => ['html']
                ]
            ),
            new \Twig_SimpleFunction('admin_setting', [$this, 'getAdminSetting']),
        ];
    }

    /**
     * Returns button which links to edit page if it is a user
     *
     * @param $environment \Twig_Environment
     * @param string $settingName
     * @param string $type
     *
     * @return string
     */
    public function showSetting($environment, $settingName, $type = 'string')
    {
        if (!$this->userSettingsManager->isAuthenticated()) {
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
     * Returns setting value
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
