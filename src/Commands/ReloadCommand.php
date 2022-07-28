<?php

/**
 * @package     FrameX (FX) CLI Plugin
 * @link        https://localzet.gitbook.io
 * 
 * @author      localzet <creator@localzet.ru>
 * 
 * @copyright   Copyright (c) 2018-2020 Zorin Projects 
 * @copyright   Copyright (c) 2020-2022 NONA Team
 * 
 * @license     https://www.localzet.ru/license GNU GPLv3 License
 */

namespace localzet\CLI\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use localzet\CLI\Application;

class ReloadCommand extends Command
{
    protected static $defaultName = 'reload';
    protected static $defaultDescription = 'Перезагрузить код. Используй -g для изящной перезагрузки.';

    protected function configure(): void
    {
        $this
            ->addOption('graceful', 'd', InputOption::VALUE_NONE, 'graceful reload');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        Application::run();
        return self::SUCCESS;
    }
}
