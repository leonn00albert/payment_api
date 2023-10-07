<?php
// bootstrap.php
require_once __DIR__ . '/../vendor/autoload.php';
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();
$isDevMode = true; // 

$config = ORMSetup::createAnnotationMetadataConfiguration(
   paths: array(__DIR__."/../src"),
   isDevMode: true,
);

$connection = DriverManager::getConnection([
    'driver' => 'pdo_mysql',
    'host' => "localhost",
    'user' => "root",
    'dbname' => "test_database",
    'password' => "root_password",
], $config);


$entityManager = new EntityManager($connection, $config);
return $entityManager;