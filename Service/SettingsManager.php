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

use Doctrine\Common\Cache\CacheProvider;
use ONGR\CookiesBundle\Cookie\Model\GenericCookie;
use ONGR\ElasticsearchBundle\Result\Aggregation\AggregationValue;
use ONGR\ElasticsearchDSL\Aggregation\TermsAggregation;
use ONGR\ElasticsearchDSL\Aggregation\TopHitsAggregation;
use ONGR\SettingsBundle\Exception\SettingNotFoundException;
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
     * Cache pool container.
     *
     * @var CacheProvider
     */
    private $cache;

    /**
     * Cookie storage for active cookies.
     *
     * @var GenericCookie
     */
    private $activeProfilesCookie;

    /**
     * Active profiles setting name to store in the cache engine.
     *
     * @var string
     */
    private $activeProfilesSettingName;

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
     * @return CacheProvider
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param CacheProvider $cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return GenericCookie
     */
    public function getActiveProfilesCookie()
    {
        return $this->activeProfilesCookie;
    }

    /**
     * @param GenericCookie $activeProfilesCookie
     */
    public function setActiveProfilesCookie($activeProfilesCookie)
    {
        $this->activeProfilesCookie = $activeProfilesCookie;
    }

    /**
     * @return string
     */
    public function getActiveProfilesSettingName()
    {
        return $this->activeProfilesSettingName;
    }

    /**
     * @param string $activeProfilesSettingName
     */
    public function setActiveProfilesSettingName($activeProfilesSettingName)
    {
        $this->activeProfilesSettingName = $activeProfilesSettingName;
    }

    /**
     * Creates setting.
     *
     * @param array        $data
     *
     * @return Setting
     */
    public function create(array $data = [])
    {
        $data = array_filter($data);
        if (!isset($data['name']) || !isset($data['type'])) {
            throw new \LogicException('Missing one of the mandatory field!');
        }

        if (!isset($data['value'])) {
            $data['value'] = 0;
        }

        $name = $data['name'];
        $existingSetting = $this->get($name);

        if ($existingSetting) {
            throw new \LogicException(sprintf('Setting %s already exists.', $name));
        }

        $settingClass = $this->repo->getClassName();
        /** @var Setting $setting */
        $setting = new $settingClass();

        #TODO Introduce array populate function in Setting document instead of this foreach.
        foreach ($data as $key => $value) {
            $setting->{'set'.ucfirst($key)}($value);
        }

        $this->manager->persist($setting);
        $this->manager->commit();

        return $setting;
    }

    /**
     * Overwrites setting parameters with given name.
     *
     * @param string      $name
     * @param array       $data
     *
     * @return Setting
     */
    public function update($name, $data = [])
    {
        $setting = $this->get($name);

        if (!$setting) {
            throw new \LogicException(sprintf('Setting %s not exist.', $name));
        }

        #TODO Add populate function to document class
        foreach ($data as $key => $value) {
            $setting->{'set'.ucfirst($key)}($value);
        }

        $this->manager->persist($setting);
        $this->manager->commit();

        return $setting;
    }

    /**
     * Deletes a setting.
     *
     * @param string    $name
     *
     * @return array
     */
    public function delete($name)
    {
        $setting = $this->repo->findOneBy(['name' => $name]);
        return $this->repo->remove($setting->getId());
    }

    /**
     * Returns setting object.
     *
     * @param string $name
     *
     * @return Setting
     */
    public function get($name)
    {
        /** @var Setting $setting */
        $setting = $this->repo->findOneBy(['name' => $name]);

        return $setting;
    }

    /**
     * Get setting value by current active profiles setting.
     *
     * @param string $name
     * @param bool $default
     *
     * @return mixed
     */
    public function getValue($name, $default = null)
    {
        $setting = $this->get($name);

        if ($setting) {
            return $setting->getValue();
        }

        return $default;
    }

    /**
     * Get all full profile information.
     *
     * @return array
     */
    public function getAllProfiles()
    {
        $profiles = [];

        $search = $this->repo->createSearch();
        $topHitsAgg = new TopHitsAggregation('documents', 20);
        $termAgg = new TermsAggregation('profiles', 'profile');
        $termAgg->addAggregation($topHitsAgg);
        $search->addAggregation($termAgg);

        $result = $this->repo->execute($search);

        /** @var Setting $activeProfiles */
        $activeProfiles = $this->getValue($this->activeProfilesSetting, []);

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

    /**
     * Get only profile names.
     *
     * @param bool $onlyActive
     *
     * @return array
     */
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
