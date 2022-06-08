<?php

namespace localzet\CLI;

use support\Container;
use support\Log;
use support\Request;
use localzet\FrameX\App;
use localzet\FrameX\Config;
use localzet\Core\Connection\TcpConnection;
use localzet\Core\Protocols\Http;
use localzet\Core\Server;
use Dotenv\Dotenv;

ini_set('display_errors', 'on');
error_reporting(E_ALL);


class Application
{
    public static function run()
    {
        $runtime_logs_path = runtime_path() . DIRECTORY_SEPARATOR . 'logs';
        if (!file_exists($runtime_logs_path) || !is_dir($runtime_logs_path)) {
            if (!mkdir($runtime_logs_path, 0777, true)) {
                throw new \RuntimeException("Не удалось создать каталог runtime/logs. Пожалуйста, проверьте разрешение.");
            }
        }

        $runtime_views_path = runtime_path() . DIRECTORY_SEPARATOR . 'views';
        if (!file_exists($runtime_views_path) || !is_dir($runtime_views_path)) {
            if (!mkdir($runtime_views_path, 0777, true)) {
                throw new \RuntimeException("Не удалось создать каталог runtime/views. Пожалуйста, проверьте разрешение.");
            }
        }

        Config::reload(config_path(), ['route', 'container']);

        Server::$onMasterReload = function () {
            if (function_exists('opcache_get_status')) {
                if ($status = opcache_get_status()) {
                    if (isset($status['scripts']) && $scripts = $status['scripts']) {
                        foreach (array_keys($scripts) as $file) {
                            opcache_invalidate($file, true);
                        }
                    }
                }
            }
        };

        $config                               = config('server');
        Server::$pidFile                      = $config['pid_file'];
        Server::$stdoutFile                   = $config['stdout_file'];
        Server::$logFile                      = $config['log_file'];
        Server::$eventLoopClass = $config['event_loop'] ?? '';
        TcpConnection::$defaultMaxPackageSize = $config['max_package_size'] ?? 10 * 1024 * 1024;
        if (property_exists(Server::class, 'statusFile')) {
            Server::$statusFile = $config['status_file'] ?? '';
        }
        // if (property_exists(Server::class, 'stopTimeout')) {
        //     Server::$stopTimeout = $config['stop_timeout'] ?? 2;
        // }

        if ($config['listen']) {
            $server = new Server($config['listen'], $config['context']);
            $property_map = [
                'name',
                'count',
                'user',
                'group',
                'reusePort',
                'transport',
                'protocol'
            ];
            foreach ($property_map as $property) {
                if (isset($config[$property])) {
                    $server->$property = $config[$property];
                }
            }

            $server->onServerStart = function ($server) {
                require_once base_path() . '/support/bootstrap.php';
                $app = new App($server, Container::instance(), Log::channel('default'), app_path(), public_path());
                Http::requestClass(config('app.request_class', config('server.request_class', Request::class)));
                $server->onMessage = [$app, 'onMessage'];
            };
        }

        // Windows does not support custom processes.
        if (\DIRECTORY_SEPARATOR === '/') {
            foreach (config('process', []) as $process_name => $config) {
                // Remove monitor process.
                if (class_exists(\Phar::class, false) && \Phar::running() && 'monitor' === $process_name) {
                    continue;
                }
                server_start($process_name, $config);
            }
            foreach (config('plugin', []) as $firm => $projects) {
                foreach ($projects as $name => $project) {
                    foreach ($project['process'] ?? [] as $process_name => $config) {
                        server_start("plugin.$firm.$name.$process_name", $config);
                    }
                }
            }
        }
        Server::runAll();
    }
}
