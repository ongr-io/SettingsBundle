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

use ONGR\SettingsBundle\Document\Profile;
use ONGR\ElasticsearchBundle\Service\Manager;
use ONGR\ElasticsearchDSL\Query\MatchAllQuery;

class ProfileManager
{
    /**
     * Elasticsearch manager
     *
     * @var Manager
     */
    private $manager;

    /**
     * ProfileFinder constructor.
     *
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Finds all profiles
     *
     * @returns array
     */
    public function getAllProfiles()
    {
        $repo = $this->manager->getRepository('ONGRSettingsBundle:Profile');
        $search = $repo->createSearch();
        $search->addQuery(new MatchAllQuery());
        return $repo->execute($search, 'array');
    }

    /**
     * Creates a new profile
     *
     * @param string $name
     * @param string $description
     * @param string $id
     */
    public function createProfile($name, $description, $id = '')
    {
        $profile = new Profile();
        if ($id != '') {
            $profile->setId($id);
        }
        $profile->setName($name);
        $profile->setDescription($description);

        $this->manager->persist($profile);
        $this->manager->commit();
        $this->manager->flush();
    }
}
