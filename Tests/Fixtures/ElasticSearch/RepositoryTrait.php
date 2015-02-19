<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Fixtures\ElasticSearch;

use ONGR\ElasticsearchBundle\ORM\Manager;
use ONGR\ElasticsearchBundle\ORM\Repository;

/**
 * Trait with common ORM manager and repository mocks.
 */
trait RepositoryTrait
{
    /**
     * Returns getClassMetadataCollectionMock.
     *
     * @param array $metadata
     * @param array $typeMap
     *
     * @return mixed
     */
    private function getClassMetadataCollectionMock($metadata = [], $typeMap = [])
    {
        $mock = $this->getMockBuilder('ONGR\ElasticsearchBundle\Mapping\ClassMetadataCollection')
            ->disableOriginalConstructor()
            ->getMock();

        $mock
            ->expects($this->any())
            ->method('getMetadata')
            ->will($this->returnValue($metadata));

        $mock
            ->expects($this->any())
            ->method('getTypeMap')
            ->will($this->returnValue($typeMap));

        return $mock;
    }

    /**
     * Returns mock of ORM Manager.
     *
     * @return Manager
     */
    protected function getOrmManagerMock()
    {
        $classMetadataMock = $this
            ->getMockBuilder('ONGR\ElasticsearchBundle\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->getMock();

        $manager = $this->getMockBuilder('ONGR\ElasticsearchBundle\ORM\Manager')
            ->disableOriginalConstructor()
            ->getMock();
        $manager->expects($this->any())
            ->method('getBundlesMapping');

        $manager->classMetadataCollection = $this->getClassMetadataCollectionMock(
            [
                'rep1' => $classMetadataMock,
                'rep2' => clone $classMetadataMock,
            ]
        );

        return $manager;
    }

    /**
     * Returns mock of ORM repository.
     *
     * @return Repository
     */
    public function getOrmRepositoryMock()
    {
        $mock = $this->getMock(
            'ONGR\ElasticsearchBundle\ORM\Repository',
            ['getBundlesMapping', 'getRepository', 'find', 'remove', 'search', 'execute', 'getTypes'],
            [$this->getOrmManagerMock(), ['ONGRSettingsBundle:Setting']  ]
        );

        return $mock;
    }
}
