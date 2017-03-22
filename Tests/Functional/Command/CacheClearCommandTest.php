<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Functional\Command;

use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use ONGR\SettingsBundle\Command\CacheClearCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CacheClearCommandTest extends AbstractElasticsearchTestCase
{
    /**
     * Tests the normal functioning of the command
     */
    public function testCacheClear()
    {
        $cache = $this->getContainer()->get('ong_settings.cache_provider');
        $commandTester = $this->getCommandTester();

        $cache->save('foo', 'bar');

        $this->assertTrue($cache->contains('foo'));
        $this->assertEquals('bar', $cache->fetch('foo'));

        $commandTester->execute(
            [
                'command' => 'ongr:settings:cache:clear',
                'setting_name' => 'foo',
            ]
        );

        $this->assertContains(
            '`foo` has been successfully cleared from cache',
            $commandTester->getDisplay()
        );
        $this->assertFalse($cache->contains('foo'));
    }

    /**
     * Tests the command when a non-existing setting is provided
     */
    public function testEmptySettingClear()
    {
        $commandTester = $this->getCommandTester();

        $commandTester->execute(
            [
                'command' => 'ongr:settings:cache:clear',
                'setting_name' => 'foo',
            ]
        );

        $this->assertContains(
            'Cache does not contain a setting named `foo`',
            $commandTester->getDisplay()
        );
    }

    /**
     * Returns cache clear command with assigned container.
     *
     * @return CommandTester
     */
    private function getCommandTester()
    {
        $app = new Application();
        $command = new CacheClearCommand();
        $command->setContainer($this->getContainer());

        $app->add($command);

        $command = $app->find('ongr:settings:cache:clear');
        $commandTester = new CommandTester($command);

        return $commandTester;
    }
}
