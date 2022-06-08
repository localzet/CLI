<?php

namespace localzet\CLI\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use localzet\CLI\Application;

class StartCommand extends Command
{
    protected static $defaultName = 'start';
    protected static $defaultDescription = 'Запуск сервера в режиме отладки. Используй -d для запуска в режиме демона.';

    protected function configure() : void
    {
        $this->addOption('daemon', 'd', InputOption::VALUE_NONE, 'DAEMON mode');
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
