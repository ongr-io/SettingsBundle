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

namespace Fox\AdminBundle\Service;

use Fox\AdminBundle\Exception\SettingNotFoundException;
use Fox\AdminBundle\Settings\SettingsContainerInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Process\Exception\LogicException;

/**
 * A service for getting an object with values from the SettingsContainer
 */
class SettingAwareServiceFactory
{
    use LoggerAwareTrait;

    /**
     * @var SettingsContainerInterface
     */
    private $settingsContainer;

    /**
     * Constructor
     *
     * @param SettingsContainerInterface $settingsContainer
     */
    public function __construct(SettingsContainerInterface $settingsContainer)
    {
        $this->settingsContainer = $settingsContainer;
    }

    /**
     * Gets object with updated settings from settingsContainer
     *
     * @param array  $callMap
     * @param object $object
     *
     * @return object
     * @throws LogicException
     */
    public function get(array $callMap, $object)
    {
        foreach ($callMap as $settingName => $setter) {

            if ($setter === null) {
                $setter = $this->guessName($settingName);
            }

            try {
                $value = $this->settingsContainer->get($settingName);
            } catch (SettingNotFoundException $ex) {
                $this->logger && $this->logger->notice("Setting '{$settingName}' was not found.");

                continue;
            }

            if (method_exists($object, $setter)) {
                $object->{$setter}($value);
            } else {
                throw new LogicException("Undefined method {$setter}().");
            }
        }

        return $object;
    }

    /**
     * Guess setter name by the given parameter
     *
     * @param string $setting
     *
     * @return string
     */
    protected function guessName($setting)
    {
        return 'set' . Container::camelize($setting);
    }
}
