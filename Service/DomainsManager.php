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

//use Fox\DDALBundle\Core\Facet;
//use Fox\DDALBundle\Core\Query;
//use Fox\DDALBundle\Core\SessionModel;
//use Fox\DDALBundle\Query\Aggregations\Terms;
//use Fox\DDALBundle\Session\SessionModelAwareInterface;
//use Fox\DDALBundle\Session\SessionModelInterface;

use ONGR\ElasticsearchBundle\DSL\Aggregation\TermsAggregation;
use ONGR\ElasticsearchBundle\ORM\Repository;

/**
 * Fetches all used domains from settings type.
 * @todo: rewiev and fix - index and data types
 */
class DomainsManager implements SessionModelAwareInterface
{
    /**
     * @var SessionModel
     */
    protected $sessionModel;

    /**
     * {@inheritdoc}
     */
    public function setSessionModel(SessionModelInterface $sessionModel)
    {
        $this->sessionModel = $sessionModel;
    }

    /**
     * Get domains list from Elasticsearch.
     *
     * @return array
     */
    public function getDomains()
    {
        $manager = $this->get('es.manager');
        $repo = $manager->getRepository('ONGRAdminBundle:Settings');

        //create aggregated domains list from all available settings
        $aggregation = new TermsAggregation('domain_agg');
        $aggregation->setField('domain');
        //create query
        $search = $repo->createSearch()->addAggregation($aggregation)->setFields(['domain']);
        //process query
        $results = $repo->execute($search, Repository::RESULTS_ARRAY); //RESULTS_RAW

        return $results;
    }
}
