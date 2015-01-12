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

use ONGR\ElasticsearchBundle\ORM\Repository;
use ONGR\ElasticsearchBundle\ORM\Manager;
use ONGR\SettingsBundle\Document\Pair;
use Elasticsearch\Common\Exceptions\Missing404Exception;

/**
 * Responsible for managing pairs actions.
 */
class PairStorage
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
     * @param Manager $manager
     */
    public function __construct(
        Manager $manager
    ) {
        $this->manager = $manager;
        $this->repository = $this->manager->getRepository('ONGRSettingsBundle:Pair');
    }

    /**
     * Returns pair value by key.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        try {
            $pair = $this->repository->find($key);
        } catch (Missing404Exception $exception) {
            return null;
        }

        return $pair->getValue();
    }

    /**
     * Sets pair value. Returns pair with values.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return Pair
     */
    public function set($key, $value)
    {
        try {
            $pair = $this->repository->find($key);
        } catch (Missing404Exception $exception) {
            $pair = new Pair();
            $pair->setId($key);
        }

        $pair->setValue($value);

        $this->save($pair);

        return $pair;
    }

    /**
     * Removes pair by key.
     *
     * @param string $key
     */
    public function remove($key)
    {
        try {
            $pair = $this->repository->find($key);

            $this->repository->remove($pair->getId());
            $this->manager->flush();
            $this->manager->refresh();
        } catch (Missing404Exception $exception) {
            // If pair wasn't found, we don't do anything.
        }
    }

    /**
     * Saves pair object.
     *
     * @param Pair $pair
     */
    private function save(Pair $pair)
    {
        $this->manager->persist($pair);
        $this->manager->commit();
        $this->manager->refresh();
    }
}
