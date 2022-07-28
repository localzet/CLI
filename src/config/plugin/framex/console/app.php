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

return [
    'enable'            => true,
    'phar_file_output_dir'    => BASE_PATH . DIRECTORY_SEPARATOR . 'build',
    'phar_filename'     => 'framex.phar',
    'signature_algorithm' => Phar::SHA256, //set the signature algorithm for a phar and apply it. The signature algorithm must be one of Phar::MD5, Phar::SHA1, Phar::SHA256, Phar::SHA512, or Phar::OPENSSL.
    'private_key_file'  => '', // The file path for certificate or OpenSSL private key file.

    //'exclude_pattern'   => '#^(?!.*(config/plugin/framex/console/app.php|framex/console/src/Commands/(PharPackCommand.php|ReloadCommand.php)|LICENSE|composer.json|.github|.idea|doc|docs|.git|.setting|runtime|test|test_old|tests|Tests|vendor-bin|.md))(.*)$#',
    'exclude_files'     => [
        '.env', 'LICENSE', 'composer.json', 'composer.lock', 'start.php'
    ]
];
