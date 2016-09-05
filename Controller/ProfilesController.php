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
        /** @var Repository $repo */
        $repo = $this->get($this->getParameter('ongr_settings.repo'));

        /** @var DocumentIterator $result */
        $result = $repo->execute(
            ($repo->createSearch())->addAggregation(new TermsAggregation('profiles', 'profile'))
        );
        /** @var AggregationValue $agg */
        foreach ($result->getAggregation('profiles') as $agg) {
            $profiles[] = $agg->getValue('key');
        }

        if (empty($profiles) || !in_array('default', $profiles)) {
            array_unshift($profiles, 'default');
        }

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
        $settingName = $this->getParameter('ongr_settings.active_profiles');
        $manager = $this->get('ongr_settings.settings_manager');

        #TODO Not sure where is the right place to put active profiles setting initiating
        if (!$manager->has($settingName)) {
            $manager->create(
                [
                    'name' => $settingName,
                    'value' => [],
                    'type' => 'hidden',
                ]
            );
        }

        $profileName = $request->get('name');
        $activeProfiles = (array)$manager->getValue($settingName, []);

        $key = array_search($profileName, $activeProfiles);
        if ($key === false) {
            $activeProfiles[] = $profileName;
        } else {
            unset($activeProfiles[$key]);
        }

        $manager->update($settingName, [
            'value' => array_values($activeProfiles)
        ]);

        $this->get('ong_settings.cache_provider')->deleteAll();

        return new JsonResponse(['error' => false]);
    }
}
