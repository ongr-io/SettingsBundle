<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Service;

use ONGR\ElasticsearchBundle\ORM\Repository;
use ONGR\ElasticsearchBundle\ORM\Manager;
use ONGR\AdminBundle\Document\Parameter;
use Exception;

/**
 * Class SettingsManager responsible for managing settings actions.
 *
 * @package ONGR\AdminBundle\Service
 */
class ParametersManager
{
    /**
     * @const Prefix for parameter id.
     */
    const ID_PREFIX = 'ongr_parameter.';

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var Repository
     */
    protected $repo;

    /**
     * Constructor.
     *
     * @param Manager $manager
     */
    public function __construct(
        Manager $manager
    ) {
        $this->manager = $manager;
        $this->repo = $this->manager->getRepository('ONGRAdminBundle:Parameter');
    }

    /**
     * Returns parameter model by key.
     *
     * @param string $key
     *
     * @return Parameter
     */
    public function get($key)
    {
        try {
            $parameter = $this->repo->find(self::ID_PREFIX . $key);
        } catch (Exception $exception) {
            $parameter = new Parameter();
            $parameter->setId(self::ID_PREFIX . $key);
            $parameter->key = $key;
        }

        return $parameter;
    }

    /**
     * Sets parameter value. Returns parameter with values.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return Parameter
     */
    public function set($key, $value)
    {
        try {
            $parameter = $this->repo->find(self::ID_PREFIX . $key);
        } catch (Exception $exception) {
            $parameter = new Parameter();
            $parameter->setId(self::ID_PREFIX . $key);
        }

        $parameter->key = $key;
        $parameter->value = json_encode($value);

        $this->save($parameter);

        return $parameter;
    }

    /**
     * Saves parameter.
     *
     * @param Parameter $parameter
     */
    public function save(Parameter $parameter)
    {
        $this->manager->persist($parameter);
        $this->manager->commit();
        $this->manager->refresh();
    }

    /**
     * Removes a parameter.
     *
     * @param Parameter $parameter
     */
    public function remove(Parameter $parameter)
    {
        $this->repo->remove($parameter->getId());
        $this->manager->flush();
        $this->manager->refresh();
    }
}
