<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Filter;

use ONGR\ElasticsearchDSL\Query\BoolQuery;
use ONGR\ElasticsearchDSL\Query\TermQuery;
use ONGR\ElasticsearchDSL\Search;
use ONGR\FilterManagerBundle\Filter\FilterState;
use ONGR\FilterManagerBundle\Filter\Widget\Search\AbstractSingleValue;
use ONGR\FilterManagerBundle\Search\SearchRequest;

/**
 * This class runs match search.
 */
class SkipHiddenSettingsFilter extends AbstractSingleValue
{
    /**
     * {@inheritdoc}
     */
    public function modifySearch(Search $search, FilterState $state = null, SearchRequest $request = null)
    {
        $hidden = new TermQuery('type', 'hidden');
        $experiment = new TermQuery('type', 'experiment');
        $search->addQuery($hidden, BoolQuery::MUST_NOT);
        $search->addQuery($experiment, BoolQuery::MUST_NOT);
    }
}
