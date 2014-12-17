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
use Elasticsearch\Common\Exceptions\Missing404Exception;

/**
 * Responsible for managing parameters actions.
 *
 * @package ONGR\AdminBundle\Service
 */
class ParametersManager
{
    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @param Manager $manager
     */
    public function __construct(
        Manager $manager
    ) {
        $this->manager = $manager;
        $this->repository = $this->manager->getRepository('ONGRAdminBundle:Parameter');
    }

    /**
     * Returns parameter value by key.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        try {
            $parameter = $this->repository->find($key);
        } catch (Missing404Exception $exception) {
            $parameter = new Parameter();
            $parameter->setId($key);
        }

        return unserialize($parameter->value);
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
            $parameter = $this->repository->find($key);
        } catch (Missing404Exception $exception) {
            $parameter = new Parameter();
            $parameter->setId($key);
        }

        $parameter->value = serialize($value);

        $this->save($parameter);

        return $parameter;
    }

    /**
     * Removes a parameter.
     *
     * @param string $key
     */
    public function remove($key)
    {
        try {
            $parameter = $this->repository->find($key);

            $this->repository->remove($parameter->getId());
            $this->manager->flush();
            $this->manager->refresh();
        } catch (Missing404Exception $exception) {
            // If parameter wasn't found, we don't do anything.
        }
    }

    /**
     * Saves parameter.
     *
     * @param Parameter $parameter
     */
    private function save(Parameter $parameter)
    {
        $this->manager->persist($parameter);
        $this->manager->commit();
        $this->manager->refresh();
    }
}
