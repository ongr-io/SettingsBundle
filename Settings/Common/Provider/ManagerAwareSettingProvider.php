<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Settings\Common\Provider;

use ONGR\AdminBundle\Document\Setting;
use ONGR\FilterManagerBundle\Filters\ViewData;
use ONGR\ElasticsearchBundle\ORM\Manager;
use ONGR\ElasticsearchBundle\ORM\Repository;
use ONGR\ElasticsearchBundle\DSL\Query\MatchQuery;
use ONGR\ElasticsearchBundle\DSL\Filter\LimitFilter;

/**
 * Provider which uses session model to get settings from database using profile.
 */
class ManagerAwareSettingProvider implements SettingsProviderInterface
{
    /**
     * @var string specific profile to be used
     */
    private $profile;

    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var int limit number of results
     */
    private $limit;

    /**
     * Constructor.
     *
     * @param string $profile Profile value.
     * @param int    $limit   Limit number of results.
     */
    public function __construct($profile = 'default', $limit = 1000)
    {
        $this->profile = $profile;
        $this->limit = $limit;
    }

    /**
     * Manager setter.
     *
     * @param Manager $manager
     */
    public function setManager(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Gets settings.
     *
     * @return array
     *
     * @throws \LogicException
     */
    public function getSettings()
    {
        if ($this->manager === null) {
            throw new \LogicException('setManager must be called before getSettings.');
        }

        /** @var Repository $repo */
        $repo = $this->manager->getRepository('ONGRAdminBundle:Setting');

        // Create query.
        $search = $repo->createSearch();

        $match = new MatchQuery($this->getProfile(), 'profile');
        $search->addQuery($match);

        $limit = new LimitFilter($this->getLimit());
        $search->addFilter($limit);

        // Process query.
        $settings = $repo->execute($search);

        $result = [];

        /** @var Setting $setting */
        foreach ($settings as $setting) {
            $result[$setting->getName()] = $setting->getData()['value'];
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }
}
