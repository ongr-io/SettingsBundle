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

use ONGR\ElasticsearchBundle\Service\Manager;
use ONGR\ElasticsearchDSL\Query\MatchAllQuery;

class ProfileManager
{
    /**
     * @var Manager
     */
    private $manager;

    /**
     * ProfileFinder constructor.
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Finds all profiles
     */
    public function findAllProfiles()
    {
        $repo = $this->manager->getRepository('ONGRSettingsBundle:Profile');
        $search = $repo->createSearch();
        $search->addQuery(new MatchAllQuery());
        return $repo->execute($search, 'array');
    }
}
