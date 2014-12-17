<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Functional\Service;

use ONGR\AdminBundle\Document\Parameter;
use ONGR\AdminBundle\Service\ParametersManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use ONGR\ElasticsearchBundle\ORM\Manager;
use ONGR\ElasticsearchBundle\DSL\Query\MatchAllQuery;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Tests for ParameterManager.
 */
class ParametersManagerTest extends WebTestCase
{
    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Tests the method set.
     */
    public function testSetParameter()
    {
        $manager = new ParametersManager(
            $this->manager
        );

        $manager->set('name0', 'test1');
        $manager->set('name1', 'test13');
        $parameter1 = $this->getParameter('name0', 'test1');
        $parameter2 = $this->getParameter('name1', 'test13');

        $repo = $this->manager->getRepository('ONGRAdminBundle:Parameter');

        $search = $repo
            ->createSearch()
            ->addQuery(new MatchAllQuery());
        $documents = $repo->execute($search);

        $expected = [$parameter1, $parameter2];

        $documents = iterator_to_array($documents);

        sort($documents);
        sort($expected);

        $this->assertEquals($expected, $documents);
    }

    /**
     * Function test removing a parameter.
     */
    public function testRemove()
    {
        $manager = new ParametersManager(
            $this->manager
        );
        $parameter_value = $manager->get('name0');

        $this->assertEquals('will not be here', $parameter_value);

        $manager->remove('name0');

        // Check if item was deleted.
        $parameter_value = $manager->get('name0');

        $this->assertEquals(null, $parameter_value);
    }

    /**
     * Test getter.
     */
    public function testGet()
    {
        $manager = new ParametersManager(
            $this->manager
        );

        // This item must exist in db.
        $parameter_value = $manager->get('name0');

        $this->assertEquals('will not be here', $parameter_value);

        // This item is not existing.
        $parameter_value = $manager->get('name13');

        $this->assertEquals(null, $parameter_value);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        static::bootKernel(['environment' => 'test_container_creation']);

        /** @var ContainerInterface container */
        $this->container = static::$kernel->getContainer();
        /** @var Manager $manager */
        $this->manager = $this->container->get('es.manager');

        $this->manager->getConnection()->dropAndCreateIndex();

        // There is something wrong with ElasticsearchTestCase method getDataArray,
        // if we don't create in here all test data, it's not existing when test is run.
        $content = new Parameter();
        $content->setId('name0');
        $content->value = serialize('will not be here');

        $this->manager->persist($content);
        $this->manager->commit();
    }

    /**
     * Creates parameter model.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return Parameter
     */
    private function getParameter($key, $value)
    {
        $parameter = new Parameter();
        $parameter->setId($key);
        $parameter->value = serialize($value);
        $parameter->setScore(1.0);

        return $parameter;
    }
}
