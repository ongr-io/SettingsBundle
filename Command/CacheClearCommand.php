<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CacheClearCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('ongr:settings:cache:clear')
            ->setDescription('Clears a selected setting from cache.')
            ->addArgument(
                'setting_name',
                InputArgument::REQUIRED,
                'Setting name to remove from cache'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        if (!$this->getContainer()->has('ong_settings.cache_provider')) {
            throw new \RuntimeException('`ong_settings.cache_provider` service not available');
        }

        $cache = $this->getContainer()->get('ong_settings.cache_provider');
        $setting = $input->getArgument('setting_name');

        if (!$cache->contains($setting)) {
            $io->note(sprintf('Cache does not contain a setting named `%s`', $setting));
            return;
        }

        $cache->delete($setting);

        $io->success(sprintf('`%s` has been successfully cleared from cache', $setting));
    }
}
