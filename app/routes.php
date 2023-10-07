<?php

declare(strict_types=1);

use App\Application\Controllers\Auth\AuthController;
use App\Application\Controllers\Docs\DocsController;
use App\Application\Controllers\Movie\MovieController;
use App\Application\Controllers\Payment\PaymentController;
use App\Utils\SeedMovies;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use OpenApi\Annotations as OA;
use Psr\Log\LoggerInterface;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $paymentControlller = new PaymentController($app->getContainer()->get(LoggerInterface::class));
    $docsController = new DocsController;
    $AuthController = new AuthController;

    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });
    $app->get('/v1/payments', $paymentControlller->index());

    $app->group("/v1/customers",function ($group) {
        $group->get('/v1/customers', 'Controller');
        $group->post('/v1/customers','Controller');
        $group->delete('/v1/customers/{id:[0-9]+}', 'Controller');
        $group->put('/v1/customers/{id:[0-9]+}', 'Controller');
        $group->get('/v1/customers/deactivate/{id:[0-9]+}','Controller');
        $group->get('/v1/customers/reactivate/{id:[0-9]+}', 'Controller');
    });

    $app->get('/swagger.json', $docsController->swaggerFile());
    $app->post('/register', $AuthController->register());
    $app->get('/docs', $docsController->index());


    $app->get('/seed', function (Request $request, Response $response) {
        $seeder = new SeedMovies();
        $seed_data = $seeder->seed();
        $db = $this->get(PDO::class);
        $db = $this->get(PDO::class);

        foreach ($seed_data as $movie) {
            $sql = "INSERT INTO movies (uid, title, year, released, runtime, overview, genre, director, actors, country, poster, imdb_id, imdb, type) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $db->prepare($sql);
            $stmt->execute([]);
        };


        return $response->withHeader('Content-Type', 'application/json');
    });
};
