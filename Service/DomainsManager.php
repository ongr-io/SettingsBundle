<?php

/*
 *************************************************************************
 * NFQ eXtremes CONFIDENTIAL
 * [2013] - [2014] NFQ eXtremes UAB
 * All Rights Reserved.
 *************************************************************************
 * NOTICE:
 * All information contained herein is, and remains the property of NFQ eXtremes UAB.
 * Dissemination of this information or reproduction of this material is strictly forbidden
 * unless prior written permission is obtained from NFQ eXtremes UAB.
 *************************************************************************
 */

namespace ONGR\AdminBundle\Service;

use Fox\DDALBundle\Core\Facet;
use Fox\DDALBundle\Core\Query;
use Fox\DDALBundle\Core\SessionModel;
use Fox\DDALBundle\Query\Aggregations\Terms;
use Fox\DDALBundle\Session\SessionModelAwareInterface;
use Fox\DDALBundle\Session\SessionModelInterface;

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
