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

namespace Fox\AdminBundle\Tests\Integration;

use Fox\AdminBundle\Model\SettingModel;
use Fox\DDALBundle\Core\Session;
use Fox\DDALBundle\Session\SessionModelInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class BaseTest extends WebTestCase
{
    /**
     * @var SessionModelInterface
     */
    protected $sessionModel;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->container = self::createClient()->getContainer();
        $this->sessionModel = $this->container->get('fox_ddal.session.admin.SettingModel');
        $this->session = $this->container->get('fox_ddal.session.admin');
        $this->session->createRepository();

        $this->createDocuments();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        $this->session && $this->session->dropRepository();
    }

    /**
     * Stores documents to Elasticsearch
     */
    protected function createDocuments()
    {
        $data = $this->getDocumentsData();

        if (empty($data)) {
            return;
        }

        foreach ($data as $documentData) {

            /** @var SettingModel $document */
            $document = $this->sessionModel->createDocument();
            $document->assign($documentData);

            $this->sessionModel->saveDocument($document);
        }

        $this->sessionModel->flush();
    }

    /**
     * Returns document data
     *
     * @return array
     */
    protected function getDocumentsData()
    {
        return [];
    }
}
