<?php
// bootstrap.php
require_once 'vendor/autoload.php';
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$isDevMode = true; // 

$config = ORMSetup::createAnnotationMetadataConfiguration(
   paths: array(__DIR__."/src"),
   isDevMode: true,
);

$connection = DriverManager::getConnection([
    'driver' => 'pdo_mysql',
    'host' => $_ENV['HOST'] ?? "127.0.0.1",
    'user' => $_ENV['USERNAME'] ?? "root",
    'dbname' => $_ENV['DATABASE'] ?? "payment_api",
    'password' => $_ENV['PASSWORD'] ?? "",
], $config);


$entityManager = new EntityManager($connection, $config);
