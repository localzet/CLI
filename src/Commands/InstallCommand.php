<?php

namespace localzet\CLI\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class InstallCommand extends Command
{
    protected static $defaultName = 'install';
    protected static $defaultDescription = 'Запуск устанощика FrameX';

    /**
     * @return void
     */
    protected function configure()
    {

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("Выполнить установку Framex");
        $install_function = "\\localzet\\FrameX\\Install::install";
        if (is_callable($install_function)) {
            $install_function();
            return self::SUCCESS;
        }
        $output->writeln('<error>Эта команда требует localzet/framex версии >= 1.0.3</error>');
        return self::FAILURE;
    }

}
