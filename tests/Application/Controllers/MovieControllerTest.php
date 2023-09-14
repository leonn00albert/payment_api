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

        $controller = new MovieController($this->db, $this->logger);
        $request = $this->createMock(Request::class);
        $response = new Response();

        $result = $controller->index()($request, $response);

        $this->assertEquals(200, $result->getStatusCode());
    }

    public function testCreate()
    {
        $controller = new MovieController($this->db, $this->logger);
        $request = $this->createMock(Request::class);
        $response = new Response();
        $jsonData = [
            'uid' => 0001123123213242324111,
            'title' => "test movie",
        ];

        $jsonBody = json_encode($jsonData);
        $streamFactory = $this->createMock(StreamFactoryInterface::class);
        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())
        ->method('getContents')
        ->willReturn($jsonBody);

        $request->expects($this->once())
        ->method('getBody')
        ->willReturn($stream);

        $result = $controller->create()($request, $response);

        $this->assertEquals(201, $result->getStatusCode());
    }

    public function testRead()
    {
        $controller = new MovieController($this->db, $this->logger);
        $request = $this->createMock(Request::class);
        $response = new Response();
        $args = ['uid' => 0001123123213242324111];
        $result = $controller->read()($request, $response, $args);

        $this->assertEquals(200, $result->getStatusCode());
    }

    public function testDelete()
    {
   
        $controller = new MovieController($this->db, $this->logger);
        $request = $this->createMock(Request::class);
        $response = new Response();
        $args = ['uid' => 0001123123213242324111];
        $result = $controller->delete()($request, $response, $args);

        $this->assertEquals(200, $result->getStatusCode());
    }


}
