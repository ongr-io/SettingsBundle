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

use ONGR\ElasticsearchBundle\DSL\Aggregation\TermsAggregation;
use ONGR\ElasticsearchBundle\ORM\Repository;
use ONGR\ElasticsearchBundle\ORM\Manager;

/**
 * Fetches all used domains from settings type.
 */
class DomainsManager
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
     * Get domains from Elasticsearch.
     *
     * @return array
     */
    public function getDomains()
    {
        $repo = $this->manager->getRepository('ONGRAdminBundle:Settings');

        // Create aggregated domains list from all available settings.
        $aggregation = new TermsAggregation('domain_agg');
        $aggregation->setField('domain');
        // Create query.
        $search = $repo->createSearch()->addAggregation($aggregation)->setFields(['domain']);
        // Process query. Get RESULTS_RAW.
        $results = $repo->execute($search, Repository::RESULTS_ARRAY);

        return $results;
    }
}
