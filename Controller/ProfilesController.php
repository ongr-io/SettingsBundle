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
use ONGR\SettingsBundle\Document\Setting;
use ONGR\SettingsBundle\Service\SettingsManager;
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
    public function getAllProfilesAction(Request $request)
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

        if (empty($profiles) || !in_array('default', $profiles)) array_unshift($profiles, 'default');

        return new JsonResponse($profiles);
    }

    /**
     * Returns a json list of profiles
     *
     * @return Response
     */
    public function getFullProfilesAction()
    {
        $profiles = $this->get('ongr_settings.settings_manager')->getAllProfiles();

        return new JsonResponse(
            ['count' => count($profiles), 'documents' => $profiles]
        );
    }

    public function toggleProfileAction(Request $request)
    {
        $setting = $this->get('ongr_settings.settings_manager')
            ->get($this->getParameter('ongr_settings.active_profiles'));

        $name = $request->get('name');

        if ($setting) {
            $activeProfiles = $setting->getValue();
        } else {
            $activeProfiles = [];
        }

        $key = array_search($name, $activeProfiles);
        if ($key === false) {
            $activeProfiles[] = $name;
        } else {
            unset($activeProfiles[$key]);
        }

        $this->get('ongr_settings.settings_manager')->update($this->getParameter('ongr_settings.active_profiles'), [
            'value' => $activeProfiles
        ]);

        $this->get('ong_settings.cache_provider')->deleteAll();

        return new JsonResponse(['error' => false]);
    }
}
