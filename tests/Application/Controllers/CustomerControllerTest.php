<?php

require __DIR__ . '/../../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

use App\Application\Controllers\Customer\CustomerController;
use App\Application\Models\Customer;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Psr\Log\LoggerInterface;

use Doctrine\ORM\EntityManager;

class CustomerControllerTest extends TestCase
{
    protected LoggerInterface $logger;
    protected EntityManager $doctrine;
    protected function setUp(): void
    {
        $_ENV["environment"] = 'test';
        parent::setUp();
        
        $doctrine = require __DIR__ . "/../../../config/doctrine.php";
        $logger = new Logger("testing_log");

        $processor = new UidProcessor();
        $logger->pushProcessor($processor);

        $handler = new StreamHandler(__DIR__ .  '/../../logs/test.log', Logger::DEBUG);
        $logger->pushHandler($handler);

        $this->logger = $logger;
        $this->doctrine = $doctrine;
    }
    public function testRead()
    {

        $controller = new CustomerController($this->doctrine, $this->logger);
        $request = $this->createMock(Request::class);
        $response = new Response();

        $result = $controller->read()($request, $response);

        $this->assertEquals(200, $result->getStatusCode());
    }
 
    public function testCreate()
    {

        $controller = new CustomerController($this->doctrine, $this->logger);
        $request = $this->createMock(Request::class);
        $response = new Response();
        $jsonData = [
            'name' => 'test',
            'email' => "test@email.com",
            'balance' => 100,
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
    public function testUpdate()
    {
        $controller = new CustomerController($this->doctrine, $this->logger);
        $request = $this->createMock(Request::class);
        $response = new Response();
        $id = $this->doctrine->getRepository(Customer::class)->findOneBy([], ['id' => 'DESC'])->getId();
        $args = [$id];
        $jsonData = [
            'name' => 'hello',
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

        $result = $controller->update()($request, $response, $args);
        $this->assertEquals(200, $result->getStatusCode());
    }
    public function testDeactivate()
    {

        $controller = new CustomerController($this->doctrine, $this->logger);
        $request = $this->createMock(Request::class);
        $response = new Response();

        $args = ["test@email.com"];
        $result = $controller->deactivate()($request, $response, $args);

        $this->assertEquals(200, $result->getStatusCode());
    }
    public function testReactivate()
    {

        $controller = new CustomerController($this->doctrine, $this->logger);
        $request = $this->createMock(Request::class);
        $response = new Response();

        $args = ["test@email.com"];
        $result = $controller->reactivate()($request, $response, $args);

        $this->assertEquals(200, $result->getStatusCode());
    }

    public function testDelete()
    {

        $controller = new CustomerController($this->doctrine, $this->logger);
        $request = $this->createMock(Request::class);
        $response = new Response();

        $args = ["test@email.com"];
        $result = $controller->delete()($request, $response, $args);

        $this->assertEquals(200, $result->getStatusCode());
    }

}
