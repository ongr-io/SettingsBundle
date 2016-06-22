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
use ONGR\ElasticsearchDSL\Aggregation\TermsAggregation;
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
}
