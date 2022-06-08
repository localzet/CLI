<?php

namespace localzet\CLI\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VersionCommand extends Command
{
    protected static $defaultName = 'version';
    protected static $defaultDescription = 'Показать версию FrameX';

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $installed_file = base_path() . '/vendor/composer/installed.php';
        if (is_file($installed_file)) {
            $version_info = include $installed_file;
        }
        $framex_framework_version = $version_info['versions']['localzet/framex']['pretty_version'] ?? '';
        $output->writeln("FrameX $framex_framework_version");
        return self::SUCCESS;
    }
}
