<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Service;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use ONGR\ElasticsearchBundle\Service\Repository;
use ONGR\ElasticsearchBundle\Service\Manager;
use ONGR\SettingsBundle\Document\Setting;

/**
 * Class SettingsManager responsible for managing settings actions.
 */
class SettingsManager
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var Repository
     */
    protected $repo;

    /**
     * @param Repository               $repo
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        $repo,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->repo = $repo;
        $this->manager = $repo->getManager();
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Overwrites setting with given name.
     *
     * @param string       $name
     * @param array        $data
     * @param bool         $force
     *
     * @return Setting
     */
    public function create($name, array $data = [], $force = false)
    {
        $existingSetting = $this->get($name);
        if ($existingSetting && !$force) {
            return false;
        }

        if ($existingSetting && $force) {
            /** @var Setting $setting */
            $setting = $existingSetting;
        } else {
            $settingClass = $this->repo->getClassName();
            /** @var Setting $setting */
            $setting = new $settingClass();
        }

        $setting->setName($name);
        
        foreach ($data as $key => $value) {
            $setting->{'set'.ucfirst($key)}($value);
        }

        $this->manager->persist($setting);
        $this->manager->commit();

        return $setting;
    }

    /**
     * Overwrites setting with given name.
     *
     * @param string      $name
     * @param array       $data
     *
     * @return Setting
     */
    public function update($name, $data)
    {
        return $this->create($name, $data, true);
    }

    /**
     * Deletes a setting.
     *
     * @param string    $name
     */
    public function delete($name)
    {
        $setting = $this->repo->findOneBy(['name' => $name]);
        $this->repo->remove($setting->getId());
    }

    /**
     * Returns setting value.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return Setting
     */
    public function get($name, $default = null)
    {
        $setting = $this->repo->findOneBy(['name' => $name]);

        if ($setting) {
            return $setting;
        } else {
            return $default;
        }
    }
}
