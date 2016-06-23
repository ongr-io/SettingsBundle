<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Controller;

use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use ONGR\ElasticsearchBundle\Service\Repository;
use ONGR\ElasticsearchDSL\Aggregation\TermsAggregation;
use ONGR\ElasticsearchDSL\Aggregation\TopHitsAggregation;
use ONGR\ElasticsearchDSL\Search;
use ONGR\ElasticsearchBundle\Result\Aggregation\AggregationValue;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ProfileController. Placeholder for settings bundle profiles page.
 */
class ProfilesController extends Controller
{
    /**
     * Renders profiles page.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request)
    {
        return $this->render(
            'ONGRSettingsBundle:Profiles:list.html.twig',
            []
        );
    }

    /**
     * Returns a json list of profiles
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getProfilesAction(Request $request)
    {
        $profiles = [];
        $repo = $this->get($this->getParameter('ongr_settings.repo'));

        /** @var DocumentIterator $result */
        $result = $repo->execute(
            (new Search())->addAggregation(new TermsAggregation('profiles', 'profile'))
        );
        /** @var AggregationValue $agg */
        foreach ($result->getAggregation('profiles') as $agg) {
            $profiles[] = $agg->getValue('key');
        }

        return new JsonResponse($profiles);
    }

    /**
     * Returns a json list of profiles
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getFullProfilesAction(Request $request)
    {
        $profiles = [];
        /** @var Repository $repo */
        $repo = $this->get($this->getParameter('ongr_settings.repo'));

        $search = $repo->createSearch();
        $topHitsAgg = new TopHitsAggregation('documents', 100);
        $termAgg = new TermsAggregation('profiles', 'profile');
        $termAgg->addAggregation($topHitsAgg);
        $search->addAggregation($termAgg);

        $result = $repo->execute($search);

        /** @var AggregationValue $agg */
        foreach ($result->getAggregation('profiles') as $agg) {
            $settings = [];
            $docs = $agg->getAggregation('documents');
            foreach ($docs['hits']['hits'] as $doc) {
                $settings[] = $doc['_source']['name'];
            }

            $profiles[] = [
                'name' => $agg->getValue('key'),
                'settings' => implode(', ', $settings),
            ];
        }

        return new JsonResponse(
            ['count' => count($profiles), 'documents' => $profiles]
        );
    }
}
