<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Integration;

use ONGR\AdminBundle\Model\SettingModel;
use ONGR\DDALBundle\Core\Session;
use ONGR\DDALBundle\Session\SessionModelInterface;
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
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->container = self::createClient()->getContainer();
        $this->sessionModel = $this->container->get('ongr_ddal.session.admin.SettingModel');
        $this->session = $this->container->get('ongr_ddal.session.admin');
        $this->session->createRepository();

        $this->createDocuments();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->session && $this->session->dropRepository();
    }

    /**
     * Stores documents to Elasticsearch.
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
     * Returns document data.
     *
     * @return array
     */
    protected function getDocumentsData()
    {
        return [];
    }
}
