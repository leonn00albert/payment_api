<?php

require __DIR__ . '/../../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use App\Application\Controllers\Movie\MovieController;
use App\Application\Controllers\Payment\PaymentController;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Psr\Log\LoggerInterface;

use Doctrine\ORM\EntityManager;

class MovieControllerTest extends TestCase
{
    protected LoggerInterface $logger;
    protected EntityManager $doctrine;
    protected function setUp(): void
    {
        $_ENV["environment"] = 'test';
        parent::setUp();
        
        $doctrine = require __DIR__ . "/../../../config/doctrine_test.php";
        $logger = new Logger("testing_log");

        $processor = new UidProcessor();
        $logger->pushProcessor($processor);

        $handler = new StreamHandler(__DIR__ .  '/../../logs/test.log', Logger::DEBUG);
        $logger->pushHandler($handler);

        $this->logger = $logger;
        $this->doctrine = $doctrine;
    }
    public function testIndex()
    {

        $controller = new PaymentController($this->doctrine, $this->logger);
        $request = $this->createMock(Request::class);
        $response = new Response();

        $result = $controller->index()($request, $response);

        $this->assertEquals(200, $result->getStatusCode());
    }

}
