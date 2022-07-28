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
use Phar;
use RuntimeException;

class PharPackCommand extends Command
{
    protected static $defaultName = 'phar:pack';
    protected static $defaultDescription = 'Может быть стоит просто упаковать проект в файлы Phar. Легко распространять и использовать.';

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->checkEnv();

        $phar_file_output_dir = config('plugin.framex.console.app.phar_file_output_dir');
        if (empty($phar_file_output_dir)) {
            throw new RuntimeException('Пожалуйста, установите каталог выходного файла phar.');
        }
        if (!file_exists($phar_file_output_dir) && !is_dir($phar_file_output_dir)) {
            if (!mkdir($phar_file_output_dir, 0777, true)) {
                throw new RuntimeException("Не удалось создать выходной каталог phar-файла. Пожалуйста, проверьте разрешение.");
            }
        }

        $phar_filename = config('plugin.framex.console.app.phar_filename');
        if (empty($phar_filename)) {
            throw new RuntimeException('Пожалуйста, установите имя файла phar.');
        }

        $phar_file = rtrim($phar_file_output_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $phar_filename;
        if (file_exists($phar_file)) {
            unlink($phar_file);
        }

        $exclude_pattern = config('plugin.framex.console.app.exclude_pattern');
        $phar = new Phar($phar_file, 0, 'framex');

        $phar->startBuffering();

        $signature_algorithm = config('plugin.framex.console.app.signature_algorithm');
        if (!in_array($signature_algorithm, [Phar::MD5, Phar::SHA1, Phar::SHA256, Phar::SHA512, Phar::OPENSSL])) {
            throw new RuntimeException('Алгоритм подписи должен быть Phar::MD5, Phar::SHA1, Phar::SHA256, Phar::SHA512, или Phar::OPENSSL.');
        }
        if ($signature_algorithm === Phar::OPENSSL) {
            $private_key_file = config('plugin.framex.console.app.private_key_file');
            if (!file_exists($private_key_file)) {
                throw new RuntimeException("Если значение алгоритма подписи 'Phar::OPENSSL', вы должны установить файл закрытого ключа.");
            }
            $private = openssl_get_privatekey(file_get_contents($private_key_file));
            $pkey = '';
            openssl_pkey_export($private, $pkey);
            $phar->setSignatureAlgorithm($signature_algorithm, $pkey);
        } else {
            $phar->setSignatureAlgorithm($signature_algorithm);
        }

        $phar->buildFromDirectory(BASE_PATH, $exclude_pattern);

        $exclude_files = config('plugin.framex.console.app.exclude_files');

        foreach ($exclude_files as $file) {
            if ($phar->offsetExists($file)) {
                $phar->delete($file);
            }
        }

        $output->writeln('Сбор файлов завершен, начинаю добавлять файлы в Phar.');

        $phar->setStub("#!/usr/bin/env php
<?php
define('IN_PHAR', true);
Phar::mapPhar('framex');
require 'phar://framex/framex';
__HALT_COMPILER();
");

        $output->writeln('Запись запросов в Phar архив и сохранение изменений');

        $phar->stopBuffering();
        unset($phar);
        return self::SUCCESS;
    }

    /**
     * @throws RuntimeException
     */
    private function checkEnv(): void
    {
        if (!class_exists(Phar::class, false)) {
            throw new RuntimeException("Расширение «Phar» требуется для сборки Phar");
        }

        if (ini_get('phar.readonly')) {
            throw new RuntimeException(
                "'phar.readonly' сейчас в 'On', phar должен установить его в 'Off' или выполнить 'php -d phar.readonly=0 ./framex phar:pack'"
            );
        }
    }
}
