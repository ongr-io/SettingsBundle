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

use ONGR\ElasticsearchBundle\Result\Aggregation\AggregationValue;
use ONGR\ElasticsearchDSL\Aggregation\TermsAggregation;
use ONGR\ElasticsearchDSL\Aggregation\TopHitsAggregation;
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
     * Symfony event dispatcher.
     *
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * Elasticsearch manager which handles setting repository.
     *
     * @var Manager
     */
    private $manager;

    /**
     * Settings repository.
     *
     * @var Repository
     */
    private $repo;

    /**
     * Active profiles setting name.
     *
     * @var string
     */
    private $activeProfilesSetting;

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
     * @return string
     */
    public function getActiveProfilesSetting()
    {
        return $this->activeProfilesSetting;
    }

    /**
     * @param string $activeProfilesSetting
     */
    public function setActiveProfilesSetting($activeProfilesSetting)
    {
        $this->activeProfilesSetting = $activeProfilesSetting;
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

    public function getAllProfiles()
    {
        $profiles = [];

        $search = $this->repo->createSearch();
        $topHitsAgg = new TopHitsAggregation('documents', 10000);
        $termAgg = new TermsAggregation('profiles', 'profile');
        $termAgg->addAggregation($topHitsAgg);
        $search->addAggregation($termAgg);

        $result = $this->repo->execute($search);

        /** @var Setting $activeProfiles */
        $activeProfiles = $this->get($this->activeProfilesSetting, []);

        /** @var AggregationValue $agg */
        foreach ($result->getAggregation('profiles') as $agg) {
            $settings = [];
            $docs = $agg->getAggregation('documents');
            foreach ($docs['hits']['hits'] as $doc) {
                $settings[] = $doc['_source']['name'];
            }
            $name = $agg->getValue('key');
            $profiles[] = [
                'active' => $activeProfiles ? in_array($agg->getValue('key'), $activeProfiles->getValue()) : false,
                'name' => $name,
                'settings' => implode(', ', $settings),
            ];
        }

        return $profiles;
    }

    public function getAllProfilesNameList($onlyActive = false)
    {
        $profiles = [];
        $allProfiles = $this->getAllProfiles();

        foreach ($allProfiles as $profile) {
            if ($onlyActive and !$profile['active']) {
                continue;
            }

            $profiles[] = $profile['name'];
        }

        return $profiles;
    }
}
