<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },

        "memcache" => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            $settings =  $settings->get('memcache');
            $memcache = new Memcached();
            $memcache->addServer($settings['host'], $settings['port']);
            return $memcache;
        },
        "doctrine" => function (ContainerInterface $c) {
            
            $isDevMode = true;
            $config = Setup::createAnnotationMetadataConfiguration([__DIR__ . "/src"], $isDevMode);

            $settings = $c->get(SettingsInterface::class);
            $dbSettings = $settings->get('db');
            $conn = [
                'driver' => 'pdo_mysql',
                'host' => $dbSettings['host'],
                'dbname' => $dbSettings['database'],
                'user' => $dbSettings['username'],
                'password' => $dbSettings['password'],
                'charset' => $dbSettings['charset'],
            ];
            
            return EntityManager::create($conn, $config);
        },
    ]);
};
