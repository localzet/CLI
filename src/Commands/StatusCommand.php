<?php

namespace localzet\CLI\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use localzet\CLI\Application;

class StatusCommand extends Command
{
    protected static $defaultName = 'status';
    protected static $defaultDescription = 'Статус сервера. Используй -d, чтобы показать статус в реальном времени.';

    protected function configure(): void
    {
        $this->addOption('live', 'd', InputOption::VALUE_NONE, 'show live status');
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
