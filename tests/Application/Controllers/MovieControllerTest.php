<?php
require __DIR__ . '/../../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();
use App\Application\Controllers\Movie\MovieController;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class MovieControllerTest extends TestCase
{
    public function testIndex()
    {
        $host = $_ENV["HOST"];
        $dbname = $_ENV["DATABASE"];
        $charset = 'utf8mb4';
        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
        $controller = new MovieController(new PDO($dsn, $_ENV["USERNAME"], $_ENV["PASSWORD"]));
    
        $request = $this->createMock(Request::class);
        $response = new Response();
    
        $result = $controller->index()($request, $response);
    
        $this->assertEquals(200, $result->getStatusCode());
    }
}