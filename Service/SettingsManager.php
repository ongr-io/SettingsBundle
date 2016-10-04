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
use ONGR\ElasticsearchDSL\Aggregation\Bucketing\TermsAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Metric\TopHitsAggregation;
use ONGR\ElasticsearchDSL\Query\TermQuery;
use ONGR\SettingsBundle\Event\Events;
use ONGR\SettingsBundle\Event\SettingActionEvent;
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
     * Active profiles list collected from es, cache and cookie.
     *
     * @var array
     */
    private $activeProfilesList = [];

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
     * @return array
     */
    public function getActiveProfilesList()
    {
        return $this->activeProfilesList;
    }

    /**
     * @param array $activeProfilesList
     */
    public function setActiveProfilesList(array $activeProfilesList)
    {
        $this->activeProfilesList = $activeProfilesList;
    }

    /**
     * @param array $activeProfilesList
     */
    public function appendActiveProfilesList(array $activeProfilesList)
    {
        $this->activeProfilesList = array_merge($this->activeProfilesList, $activeProfilesList);
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

        $this->eventDispatcher->dispatch(Events::PRE_CREATE, new SettingActionEvent($name, $data, $setting));

        #TODO Introduce array populate function in Setting document instead of this foreach.
        foreach ($data as $key => $value) {
            $setting->{'set'.ucfirst($key)}($value);
        }

        $this->manager->persist($setting);
        $this->manager->commit();

        $this->eventDispatcher->dispatch(Events::POST_CREATE, new SettingActionEvent($name, $data, $setting));

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

        $this->eventDispatcher->dispatch(Events::PRE_UPDATE, new SettingActionEvent($name, $data, $setting));

        #TODO Add populate function to document class
        foreach ($data as $key => $value) {
            $setting->{'set'.ucfirst($key)}($value);
        }

        $this->manager->persist($setting);
        $this->manager->commit();
        $this->cache->delete($name);

        $this->eventDispatcher->dispatch(Events::PRE_UPDATE, new SettingActionEvent($name, $data, $setting));

        return $setting;
    }

    /**
     * Deletes a setting.
     *
     * @param string    $name
     *
     * @throws \LogicException
     * @return array
     */
    public function delete($name)
    {
        if ($this->has($name)) {
            $this->eventDispatcher->dispatch(Events::PRE_UPDATE, new SettingActionEvent($name, [], null));

            $setting = $this->get($name);
            $this->cache->delete($name);
            $response = $this->repo->remove($setting->getId());

            $this->eventDispatcher->dispatch(Events::PRE_UPDATE, new SettingActionEvent($name, $response, $setting));

            return $response;
        }

        throw new \LogicException(sprintf('Setting with name %s doesn\'t exist.', $name));
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
        $this->eventDispatcher->dispatch(Events::PRE_GET, new SettingActionEvent($name, [], null));

        /** @var Setting $setting */
        $setting = $this->repo->findOneBy(['name.name' => $name]);

        $this->eventDispatcher->dispatch(Events::PRE_GET, new SettingActionEvent($name, [], $setting));

        return $setting;
    }

    /**
     * Returns setting object.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        /** @var Setting $setting */
        $setting = $this->repo->findOneBy(['name.name' => $name]);

        if ($setting) {
            return true;
        }

        return false;
    }

    /**
     * Get setting value by current active profiles setting.
     *
     * @param string $name
     * @param bool $default
     *
     * @return string|array|bool
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
     * Get setting value by checking also from cache engine.
     *
     * @param string $name
     * @param bool   $checkWithActiveProfiles Checks if setting is in active profile.
     *
     * @return mixed
     */
    public function getCachedValue($name, $checkWithActiveProfiles = true)
    {
        if ($this->cache->contains($name)) {
            $setting = $this->cache->fetch($name);
        } elseif ($this->has($name)) {
            $settingDocument = $this->get($name);
            $setting = [
                'value' => $settingDocument->getValue(),
                'profiles' => $settingDocument->getProfile(),
            ];
            $this->cache->save($name, $setting);
        } else {
            return null;
        }

        if ($checkWithActiveProfiles) {
            if (count(array_intersect($this->getActiveProfiles(), $setting['profiles']))) {
                return $setting['value'];
            }

            return null;
        }

        return $setting['value'];
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
        $termAgg = new TermsAggregation('profiles', 'profile.profile');
        $termAgg->addAggregation($topHitsAgg);
        $search->addAggregation($termAgg);

        $result = $this->repo->findDocuments($search);

        /** @var Setting $activeProfiles */
        $activeProfiles = $this->getValue($this->activeProfilesSettingName, []);

        /** @var AggregationValue $agg */
        foreach ($result->getAggregation('profiles') as $agg) {
            $settings = [];
            $docs = $agg->getAggregation('documents');
            foreach ($docs['hits']['hits'] as $doc) {
                $settings[] = $doc['_source']['name'];
            }
            $name = $agg->getValue('key');
            $profiles[] = [
                'active' => $activeProfiles ? in_array($agg->getValue('key'), (array)$activeProfiles) : false,
                'name' => $name,
                'settings' => implode(', ', $settings),
            ];
        }

        return $profiles;
    }

    /**
     * Returns profiles settings array
     *
     * @param string $profile
     *
     * @return array
     */
    public function getProfileSettings($profile)
    {
        $search = $this->repo->createSearch();
        $termQuery = new TermQuery('profile', $profile);
        $search->addQuery($termQuery);
        $search->setSize(1000);

        $settings = $this->repo->findArray($search);

        return $settings;
    }

    /**
     * Returns cached active profiles names list.
     *
     * @return array
     */
    public function getActiveProfiles()
    {
        if ($this->cache->contains($this->activeProfilesSettingName)) {
            $profiles = $this->cache->fetch($this->activeProfilesSettingName);
        } else {
            $profiles = [];
            $allProfiles = $this->getAllProfiles();

            foreach ($allProfiles as $profile) {
                if (!$profile['active']) {
                    continue;
                }

                $profiles[] = $profile['name'];
            }

            $this->cache->save($this->activeProfilesSettingName, $profiles);
        }

        $profiles = array_merge($profiles, $this->activeProfilesList);

        return $profiles;
    }
}
