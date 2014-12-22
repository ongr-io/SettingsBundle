<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Functional;

use ONGR\AdminBundle\Document\Setting;
use ONGR\ElasticsearchBundle\Command\IndexCreateCommand;
use ONGR\ElasticsearchBundle\Command\TypeUpdateCommand;
use ONGR\ElasticsearchBundle\Client\Connection;
use ONGR\ElasticsearchBundle\ORM\Manager;
use ONGR\ElasticsearchBundle\DSL\Query\MatchAllQuery;
use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 *  Prepares data for functional Admin testing.
 */
class PrepareAdminData extends ElasticsearchTestCase
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var Application
     */
    private $app;

    /**
     * Creates Elastic search indexes and adds test data.
     */
    public function __construct()
    {
        $this->app = new Application();
        $this->manager = $this->getManager('default', false);
        $this->connection = $this->manager->getConnection();
    }

    /**
     * Creates Elastic search indexes and adds test data.
     */
    public function createIndexSetting()
    {
        $this->app->add($this->getCreateCommand());

        // Clean indexes if exists.
        if ($this->connection->indexExists()) {
            $this->cleanUp();
        }

        // Creates index.
        $command = $this->app->find('es:index:create');
        $commandTester = new CommandTester($command);
        $arguments = [
            'command' => $command->getName(),
        ];
        $commandTester->execute($arguments);
        $indexName = $this->extractIndexName($commandTester);

        $this->connection->setIndexName($indexName);
        $repo = $this->manager->getRepository('ONGRAdminBundle:Setting');

        $search = $repo
            ->createSearch()
            ->addQuery(new MatchAllQuery());
        $repo->execute($search);
    }

    /**
     * Adds test data for Setting.
     */
    public function insertSettingData()
    {
        // Add some settings.
        $content = new Setting();
        $content->setName('Acme1');
        $content->setDescription('Acme1');
        $content->setProfile('Acme1');
        $content->setType('Acme1');
        $content->setData('Acme1');
        $this->manager->persist($content);

        $content = new Setting();
        $content->setName('Acme2');
        $content->setDescription('Acme2');
        $content->setProfile('Acme2');
        $content->setType('Acme2');
        $content->setData('Acme2');
        $this->manager->persist($content);

        $this->manager->commit();
    }

    /**
     * Load types from config, and update.
     */
    public function updateTypes()
    {
        $this->app->add($this->getUpdateCommand());

        $commandToTest = $this->app->find('es:type:update');
        $commandTester = new CommandTester($commandToTest);

        $result = $commandTester->execute(
            [
                'command' => $commandToTest->getName(),
                '--force' => true,
            ]
        );
    }

    /**
     * Cleanup indexes after test.
     */
    public function cleanUp()
    {
        $this->connection->dropIndex();
    }

    /**
     * Returns import index command with assigned container.
     *
     * @return IndexImportCommand
     */
    protected function getCreateCommand()
    {
        $command = new IndexCreateCommand();
        $command->setContainer($this->getContainer());

        return $command;
    }

    /**
     * Returns update type command with assigned container.
     *
     * @return TypeImportCommand
     */
    protected function getUpdateCommand()
    {
        $command = new TypeUpdateCommand();
        $command->setContainer($this->getContainer());

        return $command;
    }

    /**
     * Retrieves index name.
     *
     * @param CommandTester $commandTester
     *
     * @return string
     */
    protected function extractIndexName(CommandTester $commandTester)
    {
        $matches = [];
        preg_match('/Index (\S+) created./', $commandTester->getDisplay(), $matches);
        $indexName = $matches[1];

        return $indexName;
    }
}
