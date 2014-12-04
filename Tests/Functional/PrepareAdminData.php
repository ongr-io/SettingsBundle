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
use ONGR\ElasticsearchBundle\Command\IndexImportCommand;
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
     * Creates Elastic search indexes and adds test data.
     */
    public function __construct()
    {
        $manager = $this->getManager('default', false);

        $connection = $manager->getConnection();
        $this->connection = $connection;

        if ($connection->indexExists()) {
            $this->cleanUp();
        }

        $app = new Application();
        $app->add($this->getCreateCommand());

        // Creates index.
        $command = $app->find('es:index:create');
        $commandTester = new CommandTester($command);
        $arguments = [
            'command' => $command->getName(),
        ];
        $commandTester->execute($arguments);
        $indexName = $this->extractIndexName($commandTester);

        $connection->setIndexName($indexName);

        $manager = $this->getManager('default', false);

        // Add some settings.
        $content = new Setting();
        $content->name = 'Acme1';
        $content->description = 'Acme1';
        $content->profile = 'Acme1';
        $content->type = 'Acme1';
        $content->data = 'Acme1';
        $manager->persist($content);

        $content = new Setting();
        $content->name = 'Acme2';
        $content->description = 'Acme2';
        $content->profile = 'Acme2';
        $content->type = 'Acme2';
        $content->data = 'Acme2';
        $manager->persist($content);

        $manager->commit();


        $repo = $manager->getRepository('ONGRAdminBundle:Setting');
        $search = $repo
            ->createSearch()
            ->addQuery(new MatchAllQuery());
        $results = $repo->execute($search);

        $profiles = [];
        foreach ($results as $doc) {
            $profiles[] = $doc->profile;
        }
        sort($profiles);
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

    /**
     * Cleanup indexes after test.
     */
    public function cleanUp()
    {
        $this->connection->dropIndex();
    }
}
