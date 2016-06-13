<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Settings\General;

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
     * @param string       $type
     * @param string       $description
     * @param string|array $value
     * @param array        $profiles
     *
     * @throws \LogicException
     */
    public function create($name, $type, $description, $value, $profiles)
    {

        $this->manager->commit();
    }

    /**
     * Overwrites setting with given name.
     *
     * @param string       $id
     * @param string       $data
     *
     * @throws \LogicException
     */
    public function update($id, $data)
    {

        $this->manager->commit();
    }

    /**
     * Removes a setting.
     *
     * @param Setting $setting
     */
    public function remove(Setting $setting)
    {
        $this->repo->remove($setting->getId());
    }

    /**
     * Returns setting model by name and profile or creates new if $mustExist is set to FALSE.
     *
     * @param string $key
     *
     * @throws \UnexpectedValueException
     *
     * @return Setting
     */
    public function get($key, $default = null)
    {
        $setting = $this->repo->findOneBy(['key' => $key]);
        if ($setting === null) {
            return $setting;
        } else {
            return $default;
        }
    }
}
