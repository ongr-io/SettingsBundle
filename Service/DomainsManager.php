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

/**
 * Fetches all used domains from settings type
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
     * Get domains from Elasticsearch
     *
     * @return array
     */
    public function getDomains()
    {
        $aggregation = new Terms();
        $aggregation->setField('domain');

        $query = new Query();
        $query->facet->add('domain', $aggregation);
        $result = $this->sessionModel->findDocuments($query);

        $aggregations = $result->getAggregations();
        $domainFacet = [];
        if (isset($aggregations[Facet::KEY_FIELDS])) {
            $domainFacet = $aggregations[Facet::KEY_FIELDS]['domain'];
        }
        return array_keys($domainFacet);
    }
}
