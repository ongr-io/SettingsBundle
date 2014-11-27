<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Settings\Provider;

use ONGR\ElasticsearchBundle\ORM\Manager;
use ONGR\AdminBundle\Document\Setting;
use ONGR\AdminBundle\Settings\SettingsProviderInterface;

/**
 * Provider which uses session model to get settings from database using domain.
 */
class ManagerAwareProvider implements SettingsProviderInterface
{
    /**
     * @var string specific domain to be used
     */
    private $domain;

    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var int limit number of results
     */
    private $limit;

    /**
     * Constructor.
     *
     * @param string $domain Domain value.
     * @param int    $limit  Limit number of results.
     */
    public function __construct($domain = 'default', $limit = 1000)
    {
        $this->domain = $domain;
        $this->limit = $limit;
    }

    /**
     * Manager setter.
     *
     * @param Manager $manager
     */
    public function setManager(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Gets settings.
     *
     * @return array
     *
     * @throws \LogicException
     */
    public function getSettings()
    {
        if ($this->manager === null) {
            throw new \LogicException('setManager must be called before getSettings.');
        }
// todo: rewrite this
//        $query = new Query();
//        $query->filter->setMust('domain', $this->getDomain());
//        $query->filter->setLimit($this->getLimit());
//
//        $result = [];
//        try {
//            $settings = $this->sessionModel->findDocuments($query);
            /** @var SettingModel[] $settings */
//            foreach ($settings as $setting) {
//                $result[$setting->name] = $setting->data['value'];
//            }
//        } catch (ResponseException $e) {
            // Do nothing.
//        }

//        return $result;
        return null;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }
}
