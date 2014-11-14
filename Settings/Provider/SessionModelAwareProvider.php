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

namespace ONGR\AdminBundle\Settings\Provider;

use ONGR\AdminBundle\Model\SettingModel;
use ONGR\AdminBundle\Settings\SettingsProviderInterface;
use Fox\DDALBundle\Core\Query;
use Fox\DDALBundle\Exception\ResponseException;
use Fox\DDALBundle\Session\SessionModelAwareInterface;
use Fox\DDALBundle\Session\SessionModelInterface;

/**
 * Provider which uses session model to get settings from database using domain
 */
class SessionModelAwareProvider implements SettingsProviderInterface, SessionModelAwareInterface
{
    /**
     * @var string specific domain to be used
     */
    private $domain;

    /**
     * @var SessionModelInterface
     */
    private $sessionModel;

    /**
     * @var int limit number of results
     */
    private $limit;

    /**
     * Constructor
     *
     * @param string $domain Domain value
     * @param int $limit limit number of results
     */
    public function __construct($domain = 'default', $limit = 1000)
    {
        $this->domain = $domain;
        $this->limit = $limit;
    }

    /**
     * @inheritDoc
     */
    public function setSessionModel(SessionModelInterface $sessionModel)
    {
        $this->sessionModel = $sessionModel;
    }

    /**
     * @inheritDoc
     */
    public function getSettings()
    {
        if ($this->sessionModel === null) {
            throw new \LogicException('setSessionModel must be called before getSettings.');
        }

        $query = new Query();
        $query->filter->setMust('domain', $this->getDomain());
        $query->filter->setLimit($this->getLimit());

        $result = [];
        try {
            $settings = $this->sessionModel->findDocuments($query);
            /** @var SettingModel[] $settings */
            foreach ($settings as $setting) {
                $result[$setting->name] = $setting->data['value'];
            }
        } catch (ResponseException $e) {
            // do nothing
        }

        return $result;
    }

    /**
     * @inheritDoc
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
