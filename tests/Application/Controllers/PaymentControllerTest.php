<?php

require __DIR__ . '/../../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use App\Application\Controllers\Movie\MovieController;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use PDO;
use Psr\Log\LoggerInterface;

class MovieControllerTest extends TestCase
{
    protected PDO $db;
    protected LoggerInterface $logger;

    protected function setUp(): void
    {
        $_ENV["environment"] = 'test';
        parent::setUp();
        $this->db = new PDO("mysql:host=" . $_ENV["HOST"] . ";dbname=" . $_ENV["DATABASE"] . ";charset=utf8mb4", $_ENV["USERNAME"], $_ENV["PASSWORD"]);



        $logger = new Logger("testing_log");

        $processor = new UidProcessor();
        $logger->pushProcessor($processor);

        $handler = new StreamHandler(__DIR__ .  '/../../logs/test.log', Logger::DEBUG);
        $logger->pushHandler($handler);

        $this->logger = $logger;
    }
    public function testIndex()
    {

        $controller = new PaymentController($this->db, $this->logger);
        $request = $this->createMock(Request::class);
        $response = new Response();

        $result = $controller->index()($request, $response);

        $this->assertEquals(200, $result->getStatusCode());
    }

}
