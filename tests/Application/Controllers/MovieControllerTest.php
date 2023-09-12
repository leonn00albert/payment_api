<?php
require __DIR__ . '/../../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();
use App\Application\Controllers\Movie\MovieController;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use PDO;
class MovieControllerTest extends TestCase
{
    protected PDO $db;


    protected function setUp(): void
    {
        parent::setUp();
        $this->db = new PDO("mysql:host=" . $_ENV["HOST"] . ";dbname=" . $_ENV["DATABASE"] . ";charset=utf8mb4", $_ENV["USERNAME"],$_ENV["PASSWORD"]); 

    }
    public function testIndex()
    {
       
        $controller = new MovieController($this->db);
        $request = $this->createMock(Request::class);
        $response = new Response();
    
        $result = $controller->index()($request, $response);
    
        $this->assertEquals(200, $result->getStatusCode());
    }

    public function testCreate()
    {
        $controller = new MovieController($this->db);
        $request = $this->createMock(Request::class);
        $response = new Response();
        $postData = [
            'uid' => 0001,
            'title' => "test movie",
        ];
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($postData);
            
        $result = $controller->create()($request, $response);
        $this->assertEquals(201, $result->getStatusCode());
    }

    public function testRead()
    {
        $dsn = "mysql:host={$_ENV["HOST"]};dbname={$_ENV["DATABASE"]};charset=utf8mb4";
        $controller = new MovieController(new PDO($dsn, $_ENV["USERNAME"], $_ENV["PASSWORD"]));
        $request = $this->createMock(Request::class);
        $response = new Response();
        $args = ['uid' => '123'];
        $result = $controller->read()($request, $response, $args);

        $this->assertEquals(200, $result->getStatusCode());

    }

    
}