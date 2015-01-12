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

use ONGR\ElasticsearchBundle\DSL\Aggregation\TermsAggregation;
use ONGR\ElasticsearchBundle\ORM\Repository;
use ONGR\ElasticsearchBundle\ORM\Manager;

/**
 * Fetches all used profiles from settings type.
 */
class ProfilesManager
{
    /**
     * @var Manager
     */
    protected $manager;

    /**
     * Constructor.
     *
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Get profiles from Elasticsearch.
     *
     * @return array
     */
    public function getProfiles()
    {
        $repo = $this->manager->getRepository('ONGRSettingsBundle:Setting');

        // Create aggregated profiles list from all available settings.
        $aggregation = new TermsAggregation('profile_agg');
        $aggregation->setField('profile');
        // Create query.
        $search = $repo->createSearch()->addAggregation($aggregation)->setFields(['profile']);
        // Process query. Get RESULTS_RAW.
        $results = $repo->execute($search, Repository::RESULTS_ARRAY);

        return $results;
    }
}
